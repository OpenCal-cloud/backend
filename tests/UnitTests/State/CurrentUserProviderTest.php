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

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\State\CurrentUserProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentUserProviderTest extends TestCase
{
    private Security&MockObject $securityMock;

    private CurrentUserProvider $currentUserProvider;

    protected function setUp(): void
    {
        $this->securityMock        = $this->createMock(Security::class);
        $this->currentUserProvider = new CurrentUserProvider($this->securityMock);
    }

    public function testProvideReturnsUser(): void
    {
        $user = new User();
        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $operation = $this->createMock(Operation::class);

        $result = $this->currentUserProvider->provide($operation);

        self::assertInstanceOf(User::class, $result);
        self::assertSame($user, $result);
    }

    public function testProvideReturnsNullIfNoUserIsLoggedIn(): void
    {
        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $result = $this->currentUserProvider->provide($operation);

        self::assertNull($result);
    }

    public function testProvideReturnsNullIfUserIsNotInstanceOfUserClass(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $operation = $this->createMock(Operation::class);

        $result = $this->currentUserProvider->provide($operation);

        self::assertNull($result);
    }
}
