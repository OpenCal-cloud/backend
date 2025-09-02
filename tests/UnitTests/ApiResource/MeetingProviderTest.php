<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\ApiResource;

use App\ApiResource\MeetingProvider;
use PHPUnit\Framework\TestCase;

class MeetingProviderTest extends TestCase
{
    public function testIdentifier(): void
    {
        $provider = new MeetingProvider();
        $provider->setIdentifier('id1234');

        self::assertSame(
            'id1234',
            $provider->getIdentifier(),
        );
    }

    public function testName(): void
    {
        $provider = new MeetingProvider();
        $provider->setName('Provider-Name');

        self::assertSame(
            'Provider-Name',
            $provider->getName(),
        );
    }

    public function testIsAvailable(): void
    {
        $provider = new MeetingProvider();
        $provider->setAvailable(true);

        self::assertTrue($provider->isAvailable());

        $provider->setAvailable(false);

        self::assertFalse($provider->isAvailable());
    }
}
