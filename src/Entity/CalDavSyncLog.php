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

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CalDavSyncLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiFilter(SearchFilter::class, properties: [
    'calDavAuth.id' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(BooleanFilter::class, properties: ['failed'])]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: [
        'groups' => [
            'log:read',
        ],
    ],
    order: [
        'createdAt' => 'DESC',
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ORM\Entity(repositoryClass: CalDavSyncLogRepository::class)]
class CalDavSyncLog
{
    #[Groups(['log:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[Groups(['log:read'])]
    #[ORM\Column]
    private int $countItems;

    #[Groups(['log:read'])]
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[Groups(['log:read'])]
    #[ORM\Column]
    private bool $failed;

    #[Groups(['log:read'])]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'calDavSyncLogs')]
    private CalDavAuth $calDavAuth;

    #[Groups(['log:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorDetails = null;

    #[Groups(['log:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCountItems(): int
    {
        return $this->countItems;
    }

    public function setCountItems(int $countItems): static
    {
        $this->countItems = $countItems;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isFailed(): bool
    {
        return $this->failed;
    }

    public function setFailed(bool $failed): static
    {
        $this->failed = $failed;

        return $this;
    }

    public function getCalDavAuth(): CalDavAuth
    {
        return $this->calDavAuth;
    }

    public function setCalDavAuth(?CalDavAuth $calDavAuth): static
    {
        if ($calDavAuth instanceof CalDavAuth) {
            $this->calDavAuth = $calDavAuth;
        }

        return $this;
    }

    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }

    public function setErrorDetails(?string $errorDetails): static
    {
        $this->errorDetails = $errorDetails;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
