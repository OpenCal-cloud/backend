<?php

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
