<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Message;
use App\Repository\UserMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserMessageRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class UserMessage implements \JsonSerializable
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="fromUserMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="toUserMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $toUser;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    public function __construct(Id $uuid, User $from, User $to, Message $message, bool $isRead = false)
    {
        $this->id = $uuid->__toString();
        $this->fromUser = $from;
        $this->toUser = $to;
        $this->message = $message->__toString();
        $this->isRead = $isRead;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getToUser(): User
    {
        return $this->toUser;
    }

    public function getFromUser(): User
    {
        return $this->fromUser;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function markRead(): void
    {
        $this->isRead = true;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'isRead' => $this->isRead,
            'createdAt' => $this->createdAt,
            'from' => $this->fromUser,
            'to' => $this->toUser,
        ];
    }
}
