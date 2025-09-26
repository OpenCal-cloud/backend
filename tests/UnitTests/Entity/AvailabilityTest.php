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

namespace App\Tests\UnitTests\Entity;

use App\Entity\Availability;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;

class AvailabilityTest extends TestCase
{
    public function testId(): void
    {
        $availability = new Availability();
        $refClass     = new \ReflectionClass($availability);
        $prop         = $refClass->getProperty('id');
        $prop->setValue($availability, 42);

        self::assertSame(
            42,
            $availability->getId(),
        );
    }

    public function testDayOfWeek(): void
    {
        $availability = new Availability();
        $availability->setDayOfWeek('Monday');

        self::assertSame(
            'Monday',
            $availability->getDayOfWeek(),
        );
    }

    public function testStartTime(): void
    {
        $dateTime = new DateTime('08:00');

        $availability = new Availability();
        $availability->setStartTime($dateTime);

        self::assertSame(
            $dateTime,
            $availability->getStartTime(),
        );
    }

    public function testEndTime(): void
    {
        $dateTime = new DateTime('17:00');

        $availability = new Availability();
        $availability->setEndTime($dateTime);

        self::assertSame(
            $dateTime,
            $availability->getEndTime(),
        );
    }

    public function testUser(): void
    {
        $userMock = $this->createMock(User::class);

        $availability = new Availability();
        $availability->setUser($userMock);

        self::assertSame(
            $userMock,
            $availability->getUser(),
        );
    }

    public function testSetUserWithNull(): void
    {
        $availability = new Availability();

        self::expectNotToPerformAssertions();

        $availability->setUser(null);
    }
}
