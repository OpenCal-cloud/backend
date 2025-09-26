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

use App\Entity\CalDavAuth;
use App\Message\SyncCalDavCalendarMessage;
use App\Message\SyncCalDavMessage;
use App\MessageHandler\SyncCalDavMessageHandler;
use App\Repository\CalDavAuthRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncCalDavMessageHandlerTest extends TestCase
{
    private CalDavAuthRepository&MockObject $calDavAuthRepositoryMock;
    private MessageBusInterface&MockObject $messageBusMock;

    protected function setUp(): void
    {
        $this->calDavAuthRepositoryMock = $this->createMock(CalDavAuthRepository::class);
        $this->messageBusMock           = $this->createMock(MessageBusInterface::class);
    }

    public function testInvokeWithoutAuths(): void
    {
        $this->messageBusMock
            ->expects(self::never())
            ->method('dispatch');

        $this->calDavAuthRepositoryMock
            ->method('findBy')
            ->willReturn([]);

        $handler = $this->getHandler();

        $handler->__invoke(new SyncCalDavMessage());
    }

    public function testInvokeWithAuths(): void
    {
        $this->messageBusMock
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturn(new Envelope(new SyncCalDavCalendarMessage(1)));

        $this->calDavAuthRepositoryMock
            ->method('findBy')
            ->willReturn([
                $this->createMock(CalDavAuth::class),
                $this->createMock(CalDavAuth::class),
            ]);

        $handler = $this->getHandler();

        $handler->__invoke(new SyncCalDavMessage());
    }

    private function getHandler(): SyncCalDavMessageHandler
    {
        return new SyncCalDavMessageHandler(
            $this->calDavAuthRepositoryMock,
            $this->messageBusMock,
        );
    }
}
