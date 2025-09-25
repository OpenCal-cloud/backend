<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdditionalEventFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AdditionalEventFieldRepository::class)]
class AdditionalEventField
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[Groups(['event_type:read', 'event:write'])]
    #[ORM\Column(length: 255, nullable: false)]
    private string $fieldType;

    #[Groups(['event_type:read'])]
    #[ORM\Column(length: 255, nullable: false)]
    private string $label;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'additionalEventFields')]
    private EventType $eventType;

    /** @var Collection<int, AdditionalEventFieldValue> */
    #[ORM\OneToMany(targetEntity: AdditionalEventFieldValue::class, mappedBy: 'field')]
    private Collection $additionalEventFieldValues;

    #[Groups(['event_type:read'])]
    private ?string $meetingProviderIdentifier = null;

    public function __construct()
    {
        $this->additionalEventFieldValues = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function setFieldType(string $fieldType): static
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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

    /** @return Collection<int, AdditionalEventFieldValue> */
    public function getAdditionalEventFieldValues(): Collection
    {
        return $this->additionalEventFieldValues;
    }

    public function addAdditionalEventFieldValue(AdditionalEventFieldValue $additionalEventFieldValue): static
    {
        if (!$this->additionalEventFieldValues->contains($additionalEventFieldValue)) {
            $this->additionalEventFieldValues->add($additionalEventFieldValue);
            $additionalEventFieldValue->setField($this);
        }

        return $this;
    }

    public function removeAdditionalEventFieldValue(AdditionalEventFieldValue $additionalEventFieldValue): static
    {
        if ($this->additionalEventFieldValues->removeElement($additionalEventFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($additionalEventFieldValue->getField() === $this) {
                $additionalEventFieldValue->setField(null);
            }
        }

        return $this;
    }

    public function getMeetingProviderIdentifier(): ?string
    {
        return $this->meetingProviderIdentifier;
    }

    public function setMeetingProviderIdentifier(?string $meetingProviderIdentifier): self
    {
        $this->meetingProviderIdentifier = $meetingProviderIdentifier;

        return $this;
    }
}
