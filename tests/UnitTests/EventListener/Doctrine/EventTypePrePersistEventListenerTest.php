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

use App\Entity\EventType;
use App\Entity\User;
use App\EventListener\Doctrine\EventTypePrePersistEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class EventTypePrePersistEventListenerTest extends TestCase
{
    private Security&MockObject $securityMock;

    protected function setUp(): void
    {
        $this->securityMock = $this->createMock(Security::class);
    }

    public function testPrePersistWithUser(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn($this->createMock(User::class));

        $listener = new EventTypePrePersistEventListener($this->securityMock);

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock
            ->expects($this->once())
            ->method('setHost');

        $listener->prePersist($eventTypeMock);
    }

    public function testPrePersistWithoutUser(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn(null);

        $listener = new EventTypePrePersistEventListener($this->securityMock);

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock
            ->expects($this->never())
            ->method('setHost');

        $listener->prePersist($eventTypeMock);
    }
}
