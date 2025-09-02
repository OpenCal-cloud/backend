<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\MeetingProvider;

use App\MeetingProvider\JitsiMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MeetingProviderServiceTest extends TestCase
{
    private JitsiMeetingProvider&MockObject $jitsiMeetingProviderMock;

    protected function setUp(): void
    {
        $this->jitsiMeetingProviderMock = $this->createMock(JitsiMeetingProvider::class);
    }

    public function testGetMeetingProviders(): void
    {
        $service = $this->getService();

        $result = $service->getMeetingProviders();

        self::assertCount(1, $result);
    }

    public function testGetProviderByIdentifierWithResult(): void
    {
        $this->jitsiMeetingProviderMock
            ->method('getIdentifier')
            ->willReturn(JitsiMeetingProvider::PROVIDER_IDENTIFIER);

        $service = $this->getService();

        $result = $service->getProviderByIdentifier(JitsiMeetingProvider::PROVIDER_IDENTIFIER);

        self::assertInstanceOf(JitsiMeetingProvider::class, $result);
    }

    public function testGetProviderByIdentifierReturnsNull(): void
    {
        $this->jitsiMeetingProviderMock
            ->method('getIdentifier')
            ->willReturn(JitsiMeetingProvider::PROVIDER_IDENTIFIER);

        $service = $this->getService();

        $result = $service->getProviderByIdentifier('random-string');

        self::assertNull($result);
    }

    private function getService(): MeetingProviderService
    {
        return new MeetingProviderService(
            $this->jitsiMeetingProviderMock,
        );
    }
}
