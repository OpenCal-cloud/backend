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
use ApiPlatform\State\ProcessorInterface;
use App\Entity\EventType;
use App\Entity\EventTypeMeetingProvider;
use App\MeetingProvider\AbstractMeetingProvider;
use App\MeetingProvider\MeetingProviderService;
use Doctrine\ORM\EntityManagerInterface;
use function Safe\json_decode;

/** @implements ProcessorInterface<EventType, EventType> */
class EventTypeStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly MeetingProviderService $meetingProviderService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param EventType $data
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($context['request'])) {
            return $data;
        }

        $request        = $context['request'];
        $requestContent = $request->getContent();

        /** @var array<array-key, mixed> $requestDataArray */
        $requestDataArray = json_decode($requestContent, true);

        if (!isset($requestDataArray['meetingProviderIdentifiers'])) {
            return $data;
        }

        /** @var array<string> $providerIdentifiers */
        $providerIdentifiers = $requestDataArray['meetingProviderIdentifiers'];

        foreach ($data->getEventTypeMeetingProviders() as $existingProvider) {
            if (\in_array($existingProvider->getProviderIdentifier(), $providerIdentifiers, true)) {
                continue;
            }

            $this->entityManager->remove($existingProvider);
        }

        // this is required because $providerIdentifiers can be empty.
        $this->entityManager->flush();

        if (0 === \count($providerIdentifiers)) {
            return $data;
        }

        foreach ($providerIdentifiers as $identifier) {
            $providerInstance = $this->meetingProviderService->getProviderByIdentifier($identifier);

            if (!($providerInstance instanceof AbstractMeetingProvider)) {
                continue;
            }

            $providerEntity = new EventTypeMeetingProvider();
            $providerEntity
                ->setEventType($data)
                ->setEnabled(true)
                ->setProviderIdentifier($providerInstance->getIdentifier());

            $data->addEventTypeMeetingProvider($providerEntity);

            $this->entityManager->persist($providerEntity);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
