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

namespace App\Entity;

use App\Repository\UnavailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

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
