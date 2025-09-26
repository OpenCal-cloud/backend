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

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Holiday;
use OpenCal\Holidays\Data\Holidays;
use Safe\DateTimeImmutable;

/** @implements ProviderInterface<Holiday> */
class HolidaysStateProvider implements ProviderInterface
{
    public function __construct()
    {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{country: string, year: string} $filters */
        $filters = $context['filters'];

        $country = $filters['country'];
        $year    = $filters['year'];

        $holidays = new Holidays();

        $holidaysData = $holidays->getHolidays($country, \intval($year));

        $holidayObjects = [];

        /** @var array{date: string, name: string, localName: string} $holiday */
        foreach ($holidaysData as $holiday) {
            $obj = new Holiday();
            $obj
                ->setYear(\intval($year))
                ->setCountry($country)
                ->setDate(new DateTimeImmutable($holiday['date']))
                ->setName($holiday['name'])
                ->setLocalName($holiday['localName']);

            $holidayObjects[] = $obj;
        }

        return $holidayObjects;
    }
}
