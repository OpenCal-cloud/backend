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

namespace App\Tests\ApiTests\Availability;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class MonthAvailabilityTest extends ApiTestCase
{
    use MatchesSnapshots;

    #[DataProvider('availabilityMonthDataProvider')]
    public function testAvailabilityFilters(string $url, int $expectedStatusCode): void
    {
        $client = static::createClient();

        $response = $client->request('GET', $url);
        self::assertSame($expectedStatusCode, $response->getStatusCode());
    }

    /** @return array<array-key, array<int, string|int>> */
    public static function availabilityMonthDataProvider(): array
    {
        return [
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-05&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-06&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-07&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-08&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-09&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-10&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability_check/month?email=user%40example.tld&date=2025-05-11&event_type_id=1',
                Response::HTTP_OK,
            ],
        ];
    }

    public function testOneMonth(): void
    {
        $client = static::createClient();

        $response = $client->request(
            'GET',
            'http://localhost:8080/availability_check/month?page=1&event_type_id=1' .
            '&date=2025-10-01&email=user%40example.tld',
            [
                'headers' => [
                    'accept' => 'application/json',
                ],
            ],
        );

        self::assertMatchesJsonSnapshot($response->toArray());
    }
}
