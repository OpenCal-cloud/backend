<?php

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
