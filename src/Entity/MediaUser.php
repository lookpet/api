<?php

namespace App\Entity;

use App\Entity\Traits\TimestampTrait;
use App\Repository\MediaUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaUserRepository", repositoryClass=MediaUserRepository::class)
 */
class MediaUser implements \JsonSerializable
{
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity=Media::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(Media $media, UserInterface $user)
    {
        $this->media = $media;
        $this->user = $user;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function jsonSerialize()
    {
        return $this->media->jsonSerialize();
    }
}
