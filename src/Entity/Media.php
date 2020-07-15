<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Url;
use App\PetDomain\VO\Width;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Media implements \JsonSerializable
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="url", length=255)
     */
    private $publicUrl;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="height", length=255, nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="width", length=255, nullable=true)
     */
    private $width;

    public function __construct(
        UserInterface $user,
        Url $publicUrl,
        Width $width,
        Height $height
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->publicUrl = $publicUrl;
        $this->width = $width;
        $this->height = $height;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPublicUrl(): Url
    {
        return $this->publicUrl;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'publicUrl' => $this->getPublicUrl(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

    public function getHeight(): Height
    {
        return $this->height;
    }

    public function getWidth(): Width
    {
        return $this->width;
    }
}
