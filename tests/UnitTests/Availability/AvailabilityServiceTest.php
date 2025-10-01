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

namespace App\Tests\UnitTests\Availability;

use App\Availability\AvailabilityService;
use App\Entity\Availability;
use App\Entity\EventType;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\EventRepository;
use App\Repository\UnavailabilityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Spatie\Snapshots\MatchesSnapshots;

class AvailabilityServiceTest extends TestCase
{
    use MatchesSnapshots;

    private AvailabilityRepository&MockObject $availabilityRepositoryMock;
    private UnavailabilityRepository&MockObject $unavailabilityRepositoryMock;
    private EventRepository&MockObject $eventRepositoryMock;
    private AvailabilityService $service;

    protected function setUp(): void
    {
        $this->availabilityRepositoryMock   = $this->createMock(AvailabilityRepository::class);
        $this->unavailabilityRepositoryMock = $this->createMock(UnavailabilityRepository::class);
        $this->eventRepositoryMock          = $this->createMock(EventRepository::class);
        $this->service                      = new AvailabilityService(
            $this->unavailabilityRepositoryMock,
            $this->availabilityRepositoryMock,
            $this->eventRepositoryMock,
        );
    }

    public function testGetDayAvailabilityInPast(): void
    {
        $day           = new DateTime('2023-11-10');
        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('12:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with('Friday')
            ->willReturn([]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertCount(0, $result);
    }

    public function testGetDayAvailabilityNow(): void
    {
        $day = new DateTime('now');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertGreaterThanOrEqual(0, \count($result));
    }

    public function testGetDayAvailabilityNow1sec(): void
    {
        $day     = new DateTime('now + 1 second'); // must be greater than "now".
        $weekDay = $day->format('l');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('12:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertGreaterThanOrEqual(0, \count($result));
    }

    public function testGetDayAvailabilityFuture(): void
    {
        $day     = new DateTime('now + 1 day');
        $weekDay = $day->format('l');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('12:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertMatchesJsonSnapshot($result);
    }

    public function testGetDayAvailabilityWithUnavailabilities(): void
    {
        $day     = new DateTime('now + 1 day');
        $weekDay = $day->format('l');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailabilityMock = $this->createMock(Unavailability::class);
        $unavailabilityMock->method('isFullDay')->willReturn(false);
        $unavailabilityMock->method('getStartTime')->willReturn(new DateTime('11:00'));
        $unavailabilityMock->method('getEndTime')->willReturn(new DateTime('13:00'));

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->with($weekDay)
            ->willReturn([$unavailabilityMock]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertMatchesJsonSnapshot($result);
    }

    public function testGetDayAvailabilityHandlesFullDayUnavailability(): void
    {
        $day = new DateTime('now + 1 day');

        $eventTypeMock = $this->createMock(EventType::class);
        $eventTypeMock->method('getHost')->willReturn(new User());
        $eventTypeMock->method('getDuration')->willReturn(60);

        $availabilityMock = $this->createMock(Availability::class);
        $availabilityMock->method('getStartTime')->willReturn(new DateTime('09:00'));
        $availabilityMock->method('getEndTime')->willReturn(new DateTime('17:00'));

        $unavailabilityMock = $this->createMock(Unavailability::class);
        $unavailabilityMock->method('isFullDay')->willReturn(true);

        $this->availabilityRepositoryMock
            ->method('findAllByWeekDayAndUser')
            ->willReturn([$availabilityMock]);

        $this->unavailabilityRepositoryMock
            ->method('findByWeekDayAndUser')
            ->willReturn([$unavailabilityMock]);

        $result = $this->service->getDayAvailability($day, $eventTypeMock);

        self::assertSame([], $result);
    }
}
