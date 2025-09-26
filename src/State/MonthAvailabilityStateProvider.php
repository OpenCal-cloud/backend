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
use App\Availability\AvailabilityService;
use App\Entity\EventType;
use App\Repository\EventTypeRepository;
use Safe\DateTime;

/** @phpstan-ignore-next-line */
class MonthAvailabilityStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly AvailabilityService $availabilityService,
        private readonly EventTypeRepository $eventTypeRepository,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{
         *     email: string,
         *     date: string,
         *     event_type_id: string,
        } $filters */
        $filters = $context['filters'] ?? [];

        $dayDT = new DateTime($filters['date']);

        $eventType = $this
            ->eventTypeRepository
            ->find(\intval($filters['event_type_id']));

        if (!$eventType instanceof EventType) {
            return [];
        }

        $dateInterval = new \DateInterval('P1D');
        $datePeriod   = new \DatePeriod(
            new DateTime($dayDT->modify('first day of this month')->format('Y-m-d')),
            $dateInterval,
            new DateTime($dayDT->modify('last day of this month')->format('Y-m-d')),
            \DatePeriod::INCLUDE_END_DATE,
        );

        $data = [];

        foreach ($datePeriod as $day) {
            $availability = $this->availabilityService->getDayAvailability($day, $eventType);

            $data[] = [
                'day'             => $day->format('Y-m-d'),
                'count_timeslots' => \count($availability),
            ];
        }

        return $data; // @phpstan-ignore-line
    }
}
