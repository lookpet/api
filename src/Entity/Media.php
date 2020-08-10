<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\FilePath;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Mime;
use App\PetDomain\VO\Url;
use App\PetDomain\VO\Width;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository", repositoryClass=MediaRepository::class)
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
     * @ORM\JoinColumn(nullable=true)
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

    /**
     * @ORM\Column(type="file_path", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="mime", length=255)
     */
    private $mime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cloudinaryId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cloudinaryUrl;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isS3Saved = false;

    /**
     * Media constructor.
     *
     * @param UserInterface $user
     * @param FilePath $filePath
     * @param Url $publicUrl
     * @param Mime $mime
     * @param Width $width
     * @param Height $height
     * @param string|null $cloudinaryId
     * @param Url|null $cloudinaryUrl
     * @param bool $isS3Saved
     */
    public function __construct(
        ?UserInterface $user,
        FilePath $filePath,
        Url $publicUrl,
        Mime $mime,
        Width $width,
        Height $height,
        ?string $cloudinaryId = null,
        ?Url $cloudinaryUrl = null,
        bool $isS3Saved = false
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->path = $filePath;
        $this->publicUrl = $publicUrl;
        $this->width = $width;
        $this->height = $height;
        $this->mime = $mime;
        $this->cloudinaryUrl = $cloudinaryUrl;
        $this->cloudinaryId = $cloudinaryId;
        $this->isS3Saved = $isS3Saved;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPublicUrl(): Url
    {
        return $this->publicUrl;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function hasAccess(?User $user = null): bool
    {
        if ($this->getUser() === null) {
            return true;
        }

        if ($this->getUser() !== null && $user !== null) {
            return $this->getUser()->getId() === $user->getId();
        }

        return false;

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

    public function getPath(): FilePath
    {
        return $this->path;
    }

    public function getMime(): Mime
    {
        return $this->mime;
    }

    public function getCloudinaryId(): ?string
    {
        return $this->cloudinaryId;
    }

    public function setCloudinaryId(?string $cloudinaryId): self
    {
        $this->cloudinaryId = $cloudinaryId;

        return $this;
    }

    public function getCloudinaryUrl(): ?string
    {
        return $this->cloudinaryUrl;
    }

    public function setCloudinaryUrl(?string $cloudinaryUrl): self
    {
        $this->cloudinaryUrl = $cloudinaryUrl;

        return $this;
    }

    public function getIsS3Saved(): ?bool
    {
        return $this->isS3Saved;
    }

    public function setIsS3Saved(bool $isS3Saved): self
    {
        $this->isS3Saved = $isS3Saved;

        return $this;
    }
}
