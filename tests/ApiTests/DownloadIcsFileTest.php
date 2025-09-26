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

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\ApiTests\Traits\RetrieveTokenTrait;

class DownloadIcsFileTest extends ApiTestCase
{
    use RetrieveTokenTrait;

    public function testWithoutJwtAuth(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/events/1/ics');

        self::assertSame(
            401,
            $response->getStatusCode(),
        );
    }

    public function testWithAuthAnotherHost(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/events/2/ics', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'text/calendar',
            ],
        ]);

        self::assertSame(
            403,
            $response->getStatusCode(),
        );
    }

    public function testWithAuthAndAccessibleEvent(): void
    {
        $client = static::createClient();

        $token = $this->retrieveToken();

        $response = $client->request('GET', '/events/1/ics', [
            'auth_bearer' => $token,
            'headers'     => [
                'accept' => 'text/calendar',
            ],
        ]);

        /** @var string $icsFileContent */
        $icsFileContent = $response->getBrowserKitResponse()->getContent(); // @phpstan-ignore-line

        self::assertStringContainsString(
            'BEGIN:VCALENDAR',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'PRODID:-//opencal/ical//2.0/EN',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'VERSION:2.0',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'CALSCALE:GREGORIAN',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'BEGIN:VEVENT',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'SUMMARY:Conference Call',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'DESCRIPTION:Looking forward to the event!',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'DTSTART;TZID=Europe/Berlin:20240101T100000',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'DTEND;TZID=Europe/Berlin:20240101T110000',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'ORGANIZER;CN=John Doe:mailto:user@example.tld',
            $icsFileContent,
        );
        self::assertStringContainsString(
            'ATTENDEE;CN=John Doe:mailto:john.doe@example.com',
            $icsFileContent,
        );
    }
}
