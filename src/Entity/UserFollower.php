<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Uuid;
use App\Repository\UserFollowerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserFollowerRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class UserFollower
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class,  inversedBy="followers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $follower;

    public function __construct(Uuid $uuid, User $user, User $follower)
    {
        $this->id = $uuid->__toString();
        $this->user = $user;
        $this->follower = $follower;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getFollower(): User
    {
        return $this->follower;
    }
}
