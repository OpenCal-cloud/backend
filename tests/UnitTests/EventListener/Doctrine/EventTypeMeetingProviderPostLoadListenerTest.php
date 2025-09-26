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

use App\Entity\EventTypeMeetingProvider;
use App\EventListener\Doctrine\EventTypeMeetingProviderPostLoadListener;
use App\MeetingProvider\JitsiMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventTypeMeetingProviderPostLoadListenerTest extends TestCase
{
    private MeetingProviderService&MockObject $meetingProviderService;

    protected function setUp(): void
    {
        $this->meetingProviderService = $this->createMock(MeetingProviderService::class);
    }

    public function testPostLoadProviderNotFound(): void
    {
        $listener = new EventTypeMeetingProviderPostLoadListener($this->meetingProviderService);

        $this->meetingProviderService
            ->expects(self::once())
            ->method('getProviderByIdentifier')
            ->willReturn(null);

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('No meeting provider found by identifier jitsi_meet.');

        $etmpMock = $this->createMock(EventTypeMeetingProvider::class);
        $etmpMock
            ->method('getProviderIdentifier')
            ->willReturn(JitsiMeetingProvider::PROVIDER_IDENTIFIER);

        $listener->postLoad($etmpMock);
    }

    public function testPostLoadSucceeds(): void
    {
        $listener = new EventTypeMeetingProviderPostLoadListener($this->meetingProviderService);

        $this->meetingProviderService
            ->expects(self::once())
            ->method('getProviderByIdentifier')
            ->willReturn($this->createMock(JitsiMeetingProvider::class));

        $etmpMock = $this->createMock(EventTypeMeetingProvider::class);
        $etmpMock
            ->expects(self::once())
            ->method('setName');
        $etmpMock
            ->method('getProviderIdentifier')
            ->willReturn(JitsiMeetingProvider::PROVIDER_IDENTIFIER);

        $listener->postLoad($etmpMock);
    }
}
