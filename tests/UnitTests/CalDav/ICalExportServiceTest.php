<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\CalDav;

use App\CalDav\ExportEventService;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Spatie\Snapshots\MatchesSnapshots;
use function Safe\file_get_contents;
use function Safe\unlink;

class ICalExportServiceTest extends TestCase
{
    use MatchesSnapshots;

    public function testExportEvent(): void
    {
        $event = new Event();
        $event
            ->setParticipantEmail('email@someone.tld')
            ->setParticipantName('Someone')
            ->setDay(new DateTime('2021-01-01'))
            ->setStartTime(new DateTime('10:00:00'))
            ->setEndTime(new DateTime('12:00:00'))
            ->setEventType(
                (new EventType())
                    ->setName('Test')
                    ->setDuration(30)
                    ->setHost(
                        (new User())
                            ->setEmail('test@unit.tld')
                            ->setGivenName('Test')
                            ->setFamilyName('User'),
                    ),
            );

        $service = new ExportEventService('Europe/Berlin');
        $result  = $service->exportEvent($event);

        $iCalContent = file_get_contents($result);

        self::assertStringContainsString(
            'DTSTART:20210101T100000Z',
            $iCalContent,
        );
        self::assertStringContainsString(
            'DTEND:20210101T120000Z',
            $iCalContent,
        );
        self::assertStringContainsString(
            'VERSION:2.0',
            $iCalContent,
        );
        self::assertStringContainsString(
            'SUMMARY:Test',
            $iCalContent,
        );
        self::assertStringContainsString(
            'ORGANIZER;CN=Test User:mailto:test@unit.tld',
            $iCalContent,
        );
        self::assertStringContainsString(
            'ATTENDEE;CN=Someone:mailto:email@someone.tld',
            $iCalContent,
        );
        self::assertStringContainsString(
            'BEGIN:VTIMEZONE',
            $iCalContent,
        );
        self::assertStringContainsString(
            'TZID:Europe/Berlin',
            $iCalContent,
        );
        self::assertStringContainsString(
            'END:VTIMEZONE',
            $iCalContent,
        );

        unlink($result);
    }

    public function testExportWithLocation(): void
    {
        $event = new Event();
        $event
            ->setParticipantEmail('email@someone.tld')
            ->setParticipationUrl('https://meeting-domain.org/meeting-id')
            ->setParticipantName('Someone')
            ->setDay(new DateTime('2021-01-01'))
            ->setStartTime(new DateTime('10:00:00'))
            ->setEndTime(new DateTime('12:00:00'))
            ->setEventType(
                (new EventType())
                    ->setName('Test')
                    ->setDuration(30)
                    ->setHost(
                        (new User())
                            ->setEmail('test@unit.tld')
                            ->setGivenName('Test')
                            ->setFamilyName('User'),
                    ),
            );

        $service = new ExportEventService('Europe/Berlin');
        $result  = $service->exportEvent($event);

        $iCalContent = file_get_contents($result);

        self::assertStringContainsString(
            'LOCATION:https://meeting-domain.org/meeting-id',
            $iCalContent,
        );
    }

    public function testExportWithAnotherTimezone(): void
    {
        $event = new Event();
        $event
            ->setParticipantEmail('email@someone.tld')
            ->setParticipationUrl('https://meeting-domain.org/meeting-id')
            ->setParticipantName('Someone')
            ->setDay(new DateTime('2021-01-01'))
            ->setStartTime(new DateTime('10:00:00'))
            ->setEndTime(new DateTime('12:00:00'))
            ->setEventType(
                (new EventType())
                    ->setName('Test')
                    ->setDuration(30)
                    ->setHost(
                        (new User())
                            ->setEmail('test@unit.tld')
                            ->setGivenName('Test')
                            ->setFamilyName('User'),
                    ),
            );

        $service = new ExportEventService('Africa/Abidjan');
        $result  = $service->exportEvent($event);

        $iCalContent = file_get_contents($result);

        self::assertStringContainsString(
            'BEGIN:VTIMEZONE',
            $iCalContent,
        );
        self::assertStringContainsString(
            'TZID:Africa/Abidjan',
            $iCalContent,
        );
        self::assertStringContainsString(
            'END:VTIMEZONE',
            $iCalContent,
        );
    }

    public function testExportEventIsSynced(): void
    {
        $event = new Event();
        $event->setSyncHash('hash');

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('A synced event cannot be exported as .ics-file.');

        $service = new ExportEventService('Europe/Berlin');
        $service->exportEvent($event);
    }
}
