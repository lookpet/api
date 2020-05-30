<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\Repository\PetCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PetCommentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class PetComment implements \JsonSerializable
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Pet::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pet;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="petComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(UserInterface $user, string $comment, Pet $pet)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->comment = $comment;
        $this->pet = $pet;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return [
            'user' => $this->getUser()->jsonSerialize(),
            'comment' => $this->getComment(),
            'createdAt' => $this->createdAt,
        ];
    }
}
