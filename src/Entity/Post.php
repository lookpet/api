<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Post\PostDescription;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Post implements \JsonSerializable
{
    use TimestampTrait;
    use LifecycleCallbackTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
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

    /**
     * @ORM\ManyToMany(targetEntity=PostLike::class, mappedBy="post")
     */
    private $likes;

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
        $this->likes = new ArrayCollection();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->getDescription(),
            'user' => $this->user,
            'media' => $this->media,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
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

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addPost($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            $like->removePost($this);
        }

        return $this;
    }

    public function hasLike(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        /** @var PostLike $currentLike */
        foreach ($this->likes as $currentLike) {
            if ($currentLike->getUser()->equals($user)) {
                return true;
            }
        }

        return false;
    }
}
