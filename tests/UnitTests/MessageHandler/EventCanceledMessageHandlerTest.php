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

namespace App\Tests\UnitTests\MessageHandler;

use App\Entity\Event;
use App\Message\EventCanceledMessage;
use App\MessageHandler\EventCanceledMessageHandler;
use App\Notification\Email\BookingCanceledToHostEmailNotificationService;
use App\Repository\EventRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventCanceledMessageHandlerTest extends TestCase
{
    private EventRepository&MockObject $eventRepository;
    private BookingCanceledToHostEmailNotificationService&MockObject $notificationService;

    protected function setUp(): void
    {
        $this->eventRepository     = $this->createMock(EventRepository::class);
        $this->notificationService = $this->createMock(BookingCanceledToHostEmailNotificationService::class);
    }

    public function testInvokeEventFound(): void
    {
        $eventMock = $this->createMock(Event::class);

        $this->eventRepository
            ->method('find')
            ->willReturn($eventMock);

        $this->notificationService
            ->expects($this->once())
            ->method('sendNotification');

        $handler = new EventCanceledMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new EventCanceledMessage(1));
    }

    public function testInvokeEventNotFound(): void
    {
        $this->eventRepository
            ->method('find')
            ->willReturn(null);

        $this->notificationService
            ->expects($this->never())
            ->method('sendNotification');

        $handler = new EventCanceledMessageHandler(
            $this->eventRepository,
            $this->notificationService,
        );

        $handler->__invoke(new EventCanceledMessage(1));
    }
}
