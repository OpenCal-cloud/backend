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

namespace App\Tests\UnitTests\Event;

use App\Entity\Event;
use App\Event\EventService;
use App\Notification\Email\ReminderToAttendeeEmailNotification;
use App\Notification\Email\ReminderToHostEmailNotification;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Safe\DateTime;
use Safe\DateTimeImmutable;

class EventServiceTest extends TestCase
{
    private LoggerInterface&MockObject $loggerMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private ReminderToHostEmailNotification&MockObject $reminderToHostEmailNotificationMock;
    private ReminderToAttendeeEmailNotification&MockObject $reminderToAttendeeEmailNotificationMock;

    protected function setUp(): void
    {
        $this->loggerMock                              = $this->createMock(LoggerInterface::class);
        $this->entityManagerMock                       = $this->createMock(EntityManagerInterface::class);
        $this->reminderToHostEmailNotificationMock     = $this->createMock(ReminderToHostEmailNotification::class);
        $this->reminderToAttendeeEmailNotificationMock = $this->createMock(ReminderToAttendeeEmailNotification::class);
    }

    public function testCanSendRemindersAlwaysSent(): void
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getReminderSentAt')
            ->willReturn(new DateTimeImmutable('01.01.2025'));

        $service = $this->getService();

        $result = $service->canSendReminders($eventMock);

        self::assertFalse($result);
    }

    public function testCanSendRemindersDiffGreater(): void
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getReminderSentAt')
            ->willReturn(null);
        $eventMock
            ->method('getDay')
            ->willReturn(new DateTime('now'));
        $eventMock
            ->method('getStartTime')
            ->willReturn(new DateTime('now + 20min'));

        $service = $this->getService();

        $result = $service->canSendReminders($eventMock);

        self::assertFalse($result);
    }

    public function testCanSendRemindersCanSend(): void
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->method('getReminderSentAt')
            ->willReturn(null);
        $eventMock
            ->method('getDay')
            ->willReturn(new DateTime('now'));
        $eventMock
            ->method('getStartTime')
            ->willReturn(new DateTime('now + 17min'));

        $service = $this->getService();

        $result = $service->canSendReminders($eventMock);

        self::assertTrue($result);
    }

    public function testSendReminders(): void
    {
        $this->reminderToHostEmailNotificationMock
            ->expects(self::exactly(1))
            ->method('sendNotification');
        $this->reminderToAttendeeEmailNotificationMock
            ->expects(self::exactly(1))
            ->method('sendNotification');

        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->expects(self::exactly(1))
            ->method('setReminderSentAt');

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('persist');
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $service = $this->getService();
        $service->sendReminders($eventMock);
    }

    private function getService(): EventService
    {
        return new EventService(
            $this->loggerMock,
            $this->entityManagerMock,
            $this->reminderToHostEmailNotificationMock,
            $this->reminderToAttendeeEmailNotificationMock,
        );
    }
}
