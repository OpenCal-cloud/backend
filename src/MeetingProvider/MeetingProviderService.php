<?php

declare(strict_types=1);

namespace App\MeetingProvider;

class MeetingProviderService
{
    public function __construct(
        private readonly JitsiMeetingProvider $jitsiMeetingProvider,
    ) {
    }

    /** @return array<AbstractMeetingProvider> */
    public function getMeetingProviders(): array
    {
        $providers = [
            $this->jitsiMeetingProvider,
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
