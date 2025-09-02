<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EventTypeRepository;
use App\State\EventTypeStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiFilter(SearchFilter::class, properties: [
    'host.email' => 'exact',
    'slug'       => 'exact',
])]
#[ApiResource(
    operations: [
        new getCollection(),
        new Get(),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            processor: EventTypeStateProcessor::class,
        ),
        new Patch(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            processor: EventTypeStateProcessor::class,
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
    ],
    normalizationContext: [
        'groups' => [
            'event_type:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'event_type:write',
        ],
    ],
)]
#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
class EventType
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    #[Groups(['event_type:read', 'event:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 5)]
    private int $duration;

    #[ORM\Column(length: 255)]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'The slug can only contain lowercase letters, numbers and dashes.',
    )]
    private string $slug;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'eventTypes')]
    #[Groups(['event_type:read'])]
    private User $host;

    /** @var Collection<int, Event> */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'eventType')]
    private Collection $events;

    /** @var Collection<int, EventTypeMeetingProvider> */
    #[ORM\OneToMany(targetEntity: EventTypeMeetingProvider::class, mappedBy: 'eventType')]
    #[Groups(['event_type:read'])]
    #[SerializedName('meetingProviders')]
    private Collection $eventTypeMeetingProviders;

    /** @var array<string> */
    #[Groups(['event_type:write'])]
    private array $meetingProviderIdentifiers;

    public function __construct()
    {
        $this->events                    = new ArrayCollection();
        $this->eventTypeMeetingProviders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getHost(): User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        if ($host instanceof User) {
            $this->host = $host;
        }

        return $this;
    }

    /** @return Collection<int, Event> */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setEventType($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEventType() === $this) {
                $event->setEventType(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, EventTypeMeetingProvider> */
    public function getEventTypeMeetingProviders(): Collection
    {
        return $this->eventTypeMeetingProviders;
    }

    public function addEventTypeMeetingProvider(EventTypeMeetingProvider $createdAt): static
    {
        if (!$this->eventTypeMeetingProviders->contains($createdAt)) {
            $this->eventTypeMeetingProviders->add($createdAt);
            $createdAt->setEventType($this);
        }

        return $this;
    }

    public function removeEventTypeMeetingProvider(EventTypeMeetingProvider $createdAt): static
    {
        if ($this->eventTypeMeetingProviders->removeElement($createdAt)) {
            // set the owning side to null (unless already changed)
            if ($createdAt->getEventType() === $this) {
                $createdAt->setEventType(null);
            }
        }

        return $this;
    }

    /** @return array<string> */
    public function getMeetingProviderIdentifiers(): array
    {
        return $this->meetingProviderIdentifiers;
    }

    /** @param array<string> $meetingProviderIdentifiers */
    public function setMeetingProviderIdentifiers(array $meetingProviderIdentifiers): static
    {
        $this->meetingProviderIdentifiers = $meetingProviderIdentifiers;

        return $this;
    }
}
