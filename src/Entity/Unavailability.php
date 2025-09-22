<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateUnavailabilityController;
use App\Repository\UnavailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            controller: CreateUnavailabilityController::class,
        ),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => [
            'unavailabilities:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'unavailabilities:write',
        ],
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ORM\Entity(repositoryClass: UnavailabilityRepository::class)]
class Unavailability
{
    #[Groups(['unavailabilities:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'unavailabilities')]
    private User $user;

    #[Groups(['unavailabilities:read', 'unavailabilities:write'])]
    #[ORM\Column(length: 255)]
    private string $dayOfWeek;

    #[Groups(['unavailabilities:read', 'unavailabilities:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $startTime = null;

    #[Groups(['unavailabilities:read', 'unavailabilities:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $endTime = null;

    #[Groups(['unavailabilities:read', 'unavailabilities:write'])]
    #[ORM\Column(nullable: true)]
    private ?bool $fullDay = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        if ($user instanceof User) {
            $this->user = $user;
        }

        return $this;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function isFullDay(): ?bool
    {
        return $this->fullDay;
    }

    public function setFullDay(?bool $fullDay): static
    {
        $this->fullDay = $fullDay;

        return $this;
    }
}
