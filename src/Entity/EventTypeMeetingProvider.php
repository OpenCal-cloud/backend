<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventTypeMeetingProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EventTypeMeetingProviderRepository::class)]
class EventTypeMeetingProvider
{
    #[Groups(['event_type:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[Groups(['event_type:read'])]
    #[ORM\Column(length: 255)]
    private string $providerIdentifier;

    #[Groups(['event_type:read'])]
    #[ORM\Column]
    private bool $enabled;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'eventTypeMeetingProviders')]
    private EventType $eventType;

    #[Groups(['event_type:read'])]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderIdentifier(): string
    {
        return $this->providerIdentifier;
    }

    public function setProviderIdentifier(string $providerIdentifier): static
    {
        $this->providerIdentifier = $providerIdentifier;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $eventType): static
    {
        if ($eventType instanceof EventType) {
            $this->eventType = $eventType;
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
