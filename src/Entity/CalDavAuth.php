<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateCalDavAuthController;
use App\Repository\CalDavAuthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            controller: CreateCalDavAuthController::class,
        ),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => [
            'cal-dav-auth:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'cal-dav-auth:write',
        ],
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[ORM\Entity(repositoryClass: CalDavAuthRepository::class)]
class CalDavAuth
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    #[Groups(['cal-dav-auth:read'])]
    private int $id;

    #[ORM\Column]
    #[Groups(['cal-dav-auth:read', 'cal-dav-auth:write'])]
    private bool $enabled;

    #[ORM\Column(length: 255)]
    #[Groups(['cal-dav-auth:read', 'cal-dav-auth:write', 'log:read'])]
    private string $baseUri;

    #[ORM\Column(length: 255)]
    #[Groups(['cal-dav-auth:read', 'cal-dav-auth:write', 'log:read'])]
    private string $username;

    #[ORM\Column(length: 255)]
    #[Groups(['cal-dav-auth:read', 'cal-dav-auth:write'])]
    private string $password;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'calDavAuths')]
    private User $user;

    /** @var Collection<int, Event> */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'calDavAuth')]
    private Collection $events;

    /** @var Collection<int, CalDavSyncLog> */
    #[ORM\OneToMany(targetEntity: CalDavSyncLog::class, mappedBy: 'calDavAuth', orphanRemoval: true)]
    private Collection $calDavSyncLogs;

    #[ORM\Column(nullable: true)]
    #[Groups(['cal-dav-auth:read'])]
    private ?\DateTimeImmutable $syncedAt = null;

    public function __construct()
    {
        $this->events         = new ArrayCollection();
        $this->calDavSyncLogs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri): static
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUser(): User
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

    /** @return Collection<int, Event> */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCalDavAuth($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCalDavAuth() === $this) {
                $event->setCalDavAuth(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, CalDavSyncLog> */
    public function getCalDavSyncLogs(): Collection
    {
        return $this->calDavSyncLogs;
    }

    public function addCalDavSyncLog(CalDavSyncLog $calDavSyncLog): static
    {
        if (!$this->calDavSyncLogs->contains($calDavSyncLog)) {
            $this->calDavSyncLogs->add($calDavSyncLog);
            $calDavSyncLog->setCalDavAuth($this);
        }

        return $this;
    }

    public function removeCalDavSyncLog(CalDavSyncLog $calDavSyncLog): static
    {
        if ($this->calDavSyncLogs->removeElement($calDavSyncLog)) {
            // set the owning side to null (unless already changed)
            if ($calDavSyncLog->getCalDavAuth() === $this) {
                $calDavSyncLog->setCalDavAuth(null);
            }
        }

        return $this;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(?\DateTimeImmutable $syncedAt): static
    {
        $this->syncedAt = $syncedAt;

        return $this;
    }
}
