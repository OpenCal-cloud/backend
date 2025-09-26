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

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\State\DayAvailabilityStateProvider;
use App\State\MonthAvailabilityStateProvider;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/availability_check/day',
            provider: DayAvailabilityStateProvider::class,
            parameters: [
                'event_type_id' => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The event_type_id is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^\d+$/',
                            'message' => 'The event_type_id must be an integer.',
                        ]),
                        new GreaterThan([
                            'value'   => 0,
                            'message' => 'The event_type_id must be greater than 0.',
                        ]),
                    ],
                ),
                'date'          => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The date is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^(\d{4})-(\d{2})-(\d{2})$/',
                            'message' => 'The date must be in the format YYYY-MM-DD.',
                        ]),
                    ],
                ),
                'email'         => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The email is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                            'message' => 'The date must be in the format YYYY-MM-DD.',
                        ]),
                    ],
                ),
            ],
        ),
        new GetCollection(
            uriTemplate: '/availability_check/month',
            provider: MonthAvailabilityStateProvider::class,
            parameters: [
                'event_type_id' => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The event_type_id is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^\d+$/',
                            'message' => 'The event_type_id must be an integer.',
                        ]),
                        new GreaterThan([
                            'value'   => 0,
                            'message' => 'The event_type_id must be greater than 0.',
                        ]),
                    ],
                ),
                'date'          => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The date is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^(\d{4})-(\d{2})-(\d{2})$/',
                            'message' => 'The date must be in the format YYYY-MM-DD.',
                        ]),
                    ],
                ),
                'email'         => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The email is required.',
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                            'message' => 'The date must be in the format YYYY-MM-DD.',
                        ]),
                    ],
                ),
            ],
        ),
    ],
    normalizationContext: [
        'groups' => [
            'availability:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'availability:write',
        ],
    ],
)]
class Availability
{
}
