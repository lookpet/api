<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\Repository\PetLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PetLikeRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class PetLike implements \JsonSerializable
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pet::class, inversedBy="likes")
     */
    private $pet;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="petLikes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(Pet $pet, User $user, string $id)
    {
        $this->id = $id;
        $this->pet = $pet;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPet(): Pet
    {
        return $this->pet;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function equals(self $petLike): bool
    {
        return $this->getPet()->equals($petLike->getPet())
            && $this->getUser()->equals($petLike->getUser());
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->getPet()->jsonSerialize(),
            [
                'hasLike' => $this->getPet()->hasLike($this->getUser()),
                'likeCreatedAt' => $this->createdAt,
                'likeUpdatedAt' => $this->updatedAt,
            ]
        );
    }
}
