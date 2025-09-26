<?php
/*
 * Copyright (c) 2025. All Rights Reserved.
 *
 * This file is part of the OpenCal project, see https://git.var-lab.com/opencal
 *
 * You may use, distribute and modify this code under the terms of the AGPL 3.0 license,
 * which unfortunately won't be written for another century.
 *
 * Visit https://git.var-lab.com/opencal/backend/-/blob/main/LICENSE to read the full license text.
 */

declare(strict_types=1);

namespace App\CalDav;

use App\Entity\Event;
use App\Entity\EventType;
use OpenCal\iCal\Domain\Entity\Attendee;
use OpenCal\iCal\Domain\Entity\Calendar;
use OpenCal\iCal\Domain\Entity\Event as iCalEvent;
use OpenCal\iCal\Domain\Entity\TimeZone;
use OpenCal\iCal\Domain\ValueObject\DateTime;
use OpenCal\iCal\Domain\ValueObject\EmailAddress;
use OpenCal\iCal\Domain\ValueObject\Location;
use OpenCal\iCal\Domain\ValueObject\Organizer;
use OpenCal\iCal\Domain\ValueObject\TimeSpan;
use OpenCal\iCal\Presentation\Factory\CalendarFactory;
use Safe\DateTimeImmutable;
use function Safe\tempnam;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\fclose;

class ExportEventService
{
    public function __construct(
        private readonly string $timezone,
    ) {
    }

    public function exportEvent(Event $event): string
    {
        if (null !== $event->getSyncHash()) {
            throw new \RuntimeException('A synced event cannot be exported as .ics-file.');
        }

        /** @var EventType $eventType */
        $eventType = $event->getEventType();

        $iCalEvent = new iCalEvent();
        $iCalEvent
            ->setSummary($eventType->getName())
            ->setDescription($event->getParticipantMessage() ?? '')
            ->setOrganizer(new Organizer(
                new EmailAddress($eventType->getHost()->getEmail()),
                \sprintf(
                    '%s %s',
                    $eventType->getHost()->getGivenName(),
                    $eventType->getHost()->getFamilyName(),
                ),
            ))
            ->setOccurrence(
                new TimeSpan(
                    new DateTime(new DateTimeImmutable(\sprintf(
                        '%s %s',
                        $event->getDay()->format('Y-m-d'),
                        $event->getStartTime()->format('H:i:s'),
                    )), true),
                    new DateTime(new DateTimeImmutable(\sprintf(
                        '%s %s',
                        $event->getDay()->format('Y-m-d'),
                        $event->getEndTime()->format('H:i:s'),
                    )), true),
                ),
            );

        if (null !== $event->getParticipationUrl()) {
            $iCalEvent->setLocation(new Location($event->getParticipationUrl()));
        }

        if (null !== $event->getParticipantEmail()) {
            $attendee = new Attendee(
                new EmailAddress($event->getParticipantEmail()),
            );

            if (null !== $event->getParticipantName()) {
                $attendee->setDisplayName($event->getParticipantName());
            }

            $iCalEvent
                ->addAttendee($attendee);
        }

        $calendar = new Calendar([$iCalEvent]);

        $calendar->addTimeZone(new TimeZone($this->timezone));

        $componentFactory = new CalendarFactory();

        $iCalContent = $componentFactory->createCalendar($calendar)->__toString();

        $tmpFilePath = tempnam(\sys_get_temp_dir(), 'opencal_');
        $fHandle     = fopen($tmpFilePath, 'w');
        fwrite($fHandle, $iCalContent);
        fclose($fHandle);

        return $tmpFilePath;
    }
}
