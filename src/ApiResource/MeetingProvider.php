<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\MeetingProviderStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            provider: MeetingProviderStateProvider::class,
        ),
    ],
    normalizationContext: [
        'groups' => [
            'meeting_provider:read',
        ],
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
class MeetingProvider
{
    #[Groups(['meeting_provider:read'])]
    private string $identifier;

    #[Groups(['meeting_provider:read'])]
    private string $name;

    #[Groups(['meeting_provider:read'])]
    private bool $available;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }
}
