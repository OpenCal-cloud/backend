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

use App\Entity\Event;
use App\Entity\EventType;
use App\Helper\SlugHelper;
use App\MeetingProvider\JitsiMeetingProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JitsiMeetingProviderTest extends TestCase
{
    private SlugHelper&MockObject $slugHelperMock;

    protected function setUp(): void
    {
        $this->slugHelperMock = $this->createMock(SlugHelper::class);
    }

    public function testGetIdentifier(): void
    {
        $provider = $this->getProvider('http://url.tld');

        self::assertSame(
            'jitsi_meet',
            $provider->getIdentifier(),
        );
    }

    public function testGetName(): void
    {
        $provider = $this->getProvider('https://url.tld');

        self::assertSame(
            'Jitsi Meet',
            $provider->getName(),
        );
    }

    public function testGenerateMeetingUrl(): void
    {
        $provider = $this->getProvider('https://any-valid-jitsi-server-url.com');

        $eventMock     = $this->createMock(Event::class);
        $eventTypeMock = $this->createMock(EventType::class);

        $eventMock
            ->method('getEventType')
            ->willReturn($eventTypeMock);

        $this->slugHelperMock
            ->expects(self::once())
            ->method('slugify')
            ->willReturn('any-event-slug');

        $result = $provider->generateLocation($eventMock);

        self::assertSame(
            'https://any-valid-jitsi-server-url.com/any-event-slug',
            $result,
        );
    }

    public function testIsAvailable(): void
    {
        $provider = $this->getProvider('https://any-valid-jitsi-server-url.com');
        self::assertTrue($provider->isAvailable());

        $provider = $this->getProvider('https:/invalid-url');
        self::assertFalse($provider->isAvailable());

        $provider = $this->getProvider('http://no-https');
        self::assertFalse($provider->isAvailable());

        $provider = $this->getProvider('');
        self::assertFalse($provider->isAvailable());
    }

    private function getProvider(string $url): JitsiMeetingProvider
    {
        return new JitsiMeetingProvider(
            $url,
            $this->slugHelperMock,
        );
    }
}
