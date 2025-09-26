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

namespace App\Tests\UnitTests\EventListener\Doctrine;

use App\Entity\Event;
use App\EventListener\Doctrine\EventPostPersistEventListener;
use App\MeetingProvider\MeetingProviderService;
use App\Message\NewBookingMessage;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class EventPostPersistEventListenerTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBusMock;
    private MeetingProviderService $meetingProviderServiceMock;
    private EntityManagerInterface $entityManagerMock;

    protected function setUp(): void
    {
        $this->messageBusMock             = $this->createMock(MessageBusInterface::class);
        $this->meetingProviderServiceMock = $this->createMock(MeetingProviderService::class);
        $this->entityManagerMock          = $this->createMock(EntityManagerInterface::class);
    }

    public function testPostPersist(): void
    {
        $this->messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new NewBookingMessage(123)));

        $eventMock = $this->createMock(Event::class);

        $handler = new EventPostPersistEventListener(
            $this->messageBusMock,
            $this->meetingProviderServiceMock,
            $this->entityManagerMock,
        );

        $handler->postPersist($eventMock);
    }
}
