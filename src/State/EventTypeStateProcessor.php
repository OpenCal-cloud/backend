<?php

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
