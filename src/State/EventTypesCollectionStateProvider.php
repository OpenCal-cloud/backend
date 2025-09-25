<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\AdditionalEventField;
use App\Entity\EventType;
use App\MeetingProvider\MeetingProviderService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EventTypesCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly MeetingProviderService $meetingProviderService,
    ) {
    }

    private array $data;

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$operation instanceof CollectionOperationInterface) {
            return null;
        }

        $eventTypes = $this->collectionProvider->provide($operation, $uriVariables, $context);

        $data = [];

        /** @var EventType $eventType */
        foreach ($eventTypes as $eventType) {
            foreach ($eventType->getEventTypeMeetingProviders() as $eventTypeMeetingProvider) {
                $meetingProvider = $this->meetingProviderService
                    ->getProviderByIdentifier($eventTypeMeetingProvider->getProviderIdentifier());

                foreach ($meetingProvider->getRequiredFields() as $key => $config) {
                    $field = new AdditionalEventField();
                    $field
                        ->setLabel(\sprintf(
                            'booking.form.fields.provider_field.%s',
                            $key,
                        ))
                        ->setMeetingProviderIdentifier($eventTypeMeetingProvider->getProviderIdentifier())
                        ->setEventType($eventType)
                        ->setFieldType($key);

                    $eventType->addAdditionalEventField($field);
                }
            }

            $data[] = $eventType;
        }

        return $data;
    }
}
