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

class DayAvailabilityTest extends ApiTestCase
{
    use MatchesSnapshots;

    #[DataProvider('availabilityFiltersDataProvider')]
    public function testAvailabilityFilters(string $url, int $expectedStatusCode): void
    {
        $client = static::createClient();

        $response = $client->request('GET', $url);
        self::assertSame($expectedStatusCode, $response->getStatusCode());
    }

    /** @return array<array-key, array<int, string|int>> */
    public static function availabilityFiltersDataProvider(): array
    {
        return [
            ['/availability_check/day', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability_check/day?email=test%40mail.com', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability_check/day?date=2021-01-01', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability_check/day?date=2021-01-01&event_type_id=1', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability_check/day?date=2021-01-01&event_type_id=1&email=test%40mail.com', Response::HTTP_OK],
        ];
    }
}
