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
