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

namespace App\MeetingProvider;

class MeetingProviderService
{
    public function __construct(
        private readonly JitsiMeetingProvider $jitsiMeetingProvider,
        private readonly PhoneMeetingProvider $phoneMeetingProvider,
    ) {
    }

    /** @return array<AbstractMeetingProvider> */
    public function getMeetingProviders(): array
    {
        $providers = [
            $this->jitsiMeetingProvider,
            $this->phoneMeetingProvider,
        ];

        $availableProviders = [];

        foreach ($providers as $provider) {
            $availableProviders[$provider->getIdentifier()] = $provider;
        }

        return $availableProviders;
    }

    public function getProviderByIdentifier(string $identifier): ?AbstractMeetingProvider
    {
        $providers = $this->getMeetingProviders();

        if (isset($providers[$identifier])) {
            return $providers[$identifier];
        }

        return null;
    }
}
