<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdditionalEventFieldValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AdditionalEventFieldValueRepository::class)]
class AdditionalEventFieldValue
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'additionalEventFieldValues')]
    #[Groups(['event:write'])]
    private AdditionalEventField $field;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['event:write'])]
    private string $value;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'additionalEventFieldValues')]
    private Event $event;

    public function getId(): int
    {
        return $this->id;
    }

    public function getField(): AdditionalEventField
    {
        return $this->field;
    }

    public function setField(?AdditionalEventField $field): static
    {
        if ($field instanceof AdditionalEventField) {
            $this->field = $field;
        }

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        if ($event instanceof Event) {
            $this->event = $event;
        }

        return $this;
    }
}
