<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\EventType;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: EventType::class)]
class EventTypeDeleteEventListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function preRemove(EventType $eventType): void
    {
        foreach ($eventType->getEventTypeMeetingProviders() as $eventTypeMeetingProvider) {
            $this->entityManager->remove($eventTypeMeetingProvider);
        }
    }
}
