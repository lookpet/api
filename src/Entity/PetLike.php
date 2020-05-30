<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\Repository\PetLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PetLikeRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class PetLike
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

    public function __construct(Pet $pet, UserInterface $user)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->pet = $pet;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }
}
