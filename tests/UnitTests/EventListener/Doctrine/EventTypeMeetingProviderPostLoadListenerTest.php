<?php

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
