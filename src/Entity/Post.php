<?php

namespace App\Entity;

use App\PetDomain\VO\Post\PostDescription;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Media::class)
     */
    private $media;

    /**
     * @ORM\Column(type="post_description", nullable=true)
     */
    private $description;

    public function __construct(
        string $uuid,
        User $user,
        ?PostDescription $description = null,
        Media ...$mediaCollection
    ) {
        $this->id = $uuid;
        $this->user = $user;
        $this->description = $description;
        $this->media = new ArrayCollection();
        if (count($mediaCollection) > 0) {
            foreach ($mediaCollection as $media) {
                $this->addMedia($media);
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->getDescription(),
            'user' => $this->user,
            'media' => $this->media,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media ...$mediaCollection): self
    {
        foreach ($mediaCollection as $media) {
            if (!$this->media->contains($media)) {
                $this->media[] = $media;
            }
        }

        return $this;
    }

    public function removeMedia(Media ...$mediaCollection): self
    {
        foreach ($mediaCollection as $media) {
            if ($this->media->contains($media)) {
                $this->media->removeElement($media);
            }
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description === null ? '' : $this->description->__toString();
    }
}
