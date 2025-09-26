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
use App\ApiResource\MeetingProvider;
use App\MeetingProvider\MeetingProviderService;

/** @implements ProviderInterface<MeetingProvider> */
class MeetingProviderStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly MeetingProviderService $meetingProviderService,
    ) {
    }

    /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $providers = $this->meetingProviderService->getMeetingProviders();

        $meetingProviderObjects = [];

        foreach ($providers as $provider) {
            $meetingProviderObjects[] = new MeetingProvider()
                ->setName($provider->getName())
                ->setIdentifier($provider->getIdentifier())
                ->setAvailable($provider->isAvailable());
        }

        return $meetingProviderObjects;
    }
}
