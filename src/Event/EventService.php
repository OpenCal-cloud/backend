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

namespace App\Event;

use App\Entity\Event;
use App\Notification\Email\ReminderToAttendeeEmailNotification;
use App\Notification\Email\ReminderToHostEmailNotification;
use DateTimeImmutable as NativeDateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Safe\DateTimeImmutable;

class EventService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly ReminderToHostEmailNotification $reminderToHostEmailNotification,
        private readonly ReminderToAttendeeEmailNotification $reminderToAttendeeEmailNotification,
    ) {
    }

    public function canSendReminders(Event $event): bool
    {
        if ($event->getReminderSentAt() instanceof NativeDateTimeImmutable) {
            return false;
        }

        $now                = new DateTimeImmutable('now');
        $eventStartDateTime = new DateTimeImmutable(\sprintf(
            '%s %s',
            $event->getDay()->format('Y-m-d'),
            $event->getStartTime()->format('H:i:s'),
        ));

        $dateDiff = $now->diff($eventStartDateTime);

        $minutes  = false !== $dateDiff->days ? $dateDiff->days : 0 * 24 * 60;
        $minutes += $dateDiff->h * 60;
        $minutes += $dateDiff->i;

        $this->logger->info(\sprintf(
            'Event #%s: Start at %s %s; date diff between now: %s minutes; Reminder not sent.',
            $event->getId(),
            $event->getDay()->format('Y-m-d'),
            $event->getStartTime()->format('H:i:s'),
            $minutes,
        ));

        return $minutes < 18;
    }

    public function sendReminders(Event $event): void
    {
        $this->reminderToHostEmailNotification->sendNotification($event);
        $this->reminderToAttendeeEmailNotification->sendNotification($event);

        $event->setReminderSentAt(new DateTimeImmutable());
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }
}
