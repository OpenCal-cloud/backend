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
class DayAvailabilityStateProvider implements ProviderInterface
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

        $availabilities = $this
            ->availabilityService
            ->getDayAvailability($dayDT, $eventType);

        /** @var array<string, array<string>|string> $result */
        $result = [
            'day_of_week'    => \strtolower($dayDT->format('l')),
            'event_type'     => [
                'id'   => $eventType->getId(),
                'name' => $eventType->getName(),
            ],
            'availabilities' => $availabilities,
        ];

        return $result; // @phpstan-ignore-line to return a array is also ok
    }
}
