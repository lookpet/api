<?php

namespace App\Entity;

use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\Repository\UserEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserEventRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class UserEvent
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $medium;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $campaign;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $context = [];

    public function __construct(EventType $type, User $user, Utm $utm, ?EventContext $eventContext = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->type = $type->__toString();
        $this->user = $user;
        $this->source = $utm->getSource();
        $this->medium = $utm->getMedium();
        $this->campaign = $utm->getCampaign();
        if ($eventContext !== null) {
            $this->context = $eventContext->get();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function getCampaign(): ?string
    {
        return $this->campaign;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    final public function prePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getContext(): ?array
    {
        return $this->context;
    }
}
