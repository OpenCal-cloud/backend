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

use App\Entity\User;
use App\Message\PasswordRequestedMessage;
use App\MessageHandler\PasswordRequestedMessageHandler;
use App\Repository\UserRepository;
use App\User\UserMailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordRequestedMessageHandlerTest extends TestCase
{
    private UserRepository&MockObject $userRepositoryMock;
    private UserMailService&MockObject $userMailServiceMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock  = $this->createMock(UserRepository::class);
        $this->userMailServiceMock = $this->createMock(UserMailService::class);
    }

    public function testInvokeUserNotFound(): void
    {
        $this->userRepositoryMock
            ->method('find')
            ->willReturn(null);

        $this->userMailServiceMock
            ->expects(self::never())
            ->method('sendPasswordResetEmail');

        $msg = new PasswordRequestedMessage(123);

        $handler = $this->getHandler();
        $handler->__invoke($msg);
    }

    public function testInvokeSucceeds(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->method('find')
            ->willReturn($userMock);

        $msg = new PasswordRequestedMessage(123);

        $this->userMailServiceMock
            ->expects(self::once())
            ->method('sendPasswordResetEmail');

        $handler = $this->getHandler();
        $handler->__invoke($msg);
    }

    private function getHandler(): PasswordRequestedMessageHandler
    {
        return new PasswordRequestedMessageHandler(
            $this->userRepositoryMock,
            $this->userMailServiceMock,
        );
    }
}
