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

namespace App\Tests\UnitTests\MeetingProvider;

use App\MeetingProvider\JitsiMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use App\MeetingProvider\PhoneMeetingProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MeetingProviderServiceTest extends TestCase
{
    private JitsiMeetingProvider&MockObject $jitsiMeetingProviderMock;
    private PhoneMeetingProvider&MockObject $phoneMeetingProviderMock;

    protected function setUp(): void
    {
        $this->jitsiMeetingProviderMock = $this->createMock(JitsiMeetingProvider::class);
        $this->phoneMeetingProviderMock = $this->createMock(PhoneMeetingProvider::class);
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
            $this->phoneMeetingProviderMock,
        );
    }
}
