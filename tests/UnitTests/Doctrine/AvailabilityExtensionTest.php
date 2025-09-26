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

namespace App\Tests\UnitTests\Doctrine;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Doctrine\AvailabilityExtension;
use App\Entity\Availability;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class AvailabilityExtensionTest extends TestCase
{
    private Security&MockObject $securityMock;
    private QueryBuilder&MockObject $queryBuilderMock;

    private AvailabilityExtension $eventTypeExtension;

    protected function setUp(): void
    {
        $this->securityMock     = $this->createMock(Security::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);

        $this->eventTypeExtension = new AvailabilityExtension($this->securityMock);
    }

    public function testApplyToCollectionAddsWhereConditionForAvailability(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getId')
            ->willReturn(42);

        $this->securityMock
            ->method('getUser')
            ->willReturn($user);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['e']);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with('e.user = :current_user');

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('current_user', 42);

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            Availability::class,
            $this->createMock(Operation::class),
            [],
        );
    }

    public function testApplyToItemAddsWhereConditionForAvailability(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getId')
            ->willReturn(42);

        $this->securityMock
            ->method('getUser')
            ->willReturn($user);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['e']);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with('e.user = :current_user');

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('current_user', 42);

        $this->eventTypeExtension->applyToItem(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            Availability::class,
            ['id' => 1],
            $this->createMock(Operation::class),
        );
    }

    public function testAddWhereDoesNothingIfResourceClassIsNotAvailability(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn(new User());

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere');

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('setParameter');

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            'OtherResourceClass', // @phpstan-ignore-line
        );
    }

    public function testAddWhereDoesNothingIfUserIsNotInstanceOfUser(): void
    {
        $this->securityMock
            ->method('getUser')
            ->willReturn(null);

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere');

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('setParameter');

        $this->eventTypeExtension->applyToCollection(
            $this->queryBuilderMock,
            $this->createMock(QueryNameGeneratorInterface::class),
            Availability::class,
        );
    }
}
