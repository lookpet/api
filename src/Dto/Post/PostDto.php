<?php

declare(strict_types=1);

namespace App\Dto\Post;

use App\Entity\Media;
use App\Entity\Pet;
use App\Entity\PetComment;
use App\Entity\PetLike;
use App\Entity\User;
use App\PetDomain\VO\Post\PostDescription;
use Swagger\Annotations as SWG;

final class PostDto
{
    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet id",
     *     example="dog",
     * )
     */
    private string $id;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="post short",
     *     example="rex2020",
     * )
     */
    private ?string $slug = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="post description",
     *     example="rex",
     * )
     */
    private ?PostDescription $description = null;

    private User $user;

    /**
     * @var Media[]
     */
    private array $media = [];

    /**
     * @var PetComment[]
     */
    private array $comments = [];

    /**
     * @var PetLike[]
     */
    private array $petLikes = [];

    /**
     * @var Pet[]
     */
    private array $pets = [];

    public function __construct(string $id, User $user, ?PostDescription $description = null)
    {
        $this->id = $id;
        $this->user = $user;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return PostDescription|null
     */
    public function getDescription(): ?PostDescription
    {
        return $this->description;
    }

    /**
     * @param PostDescription|null $description
     */
    public function setDescription(?PostDescription $description): void
    {
        $this->description = $description;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Media[]
     */
    public function getMedia(): array
    {
        return $this->media;
    }

    /**
     * @param Media[] $media
     */
    public function setMedia(Media ...$media): void
    {
        $this->media = $media;
    }

    /**
     * @return PetComment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param PetComment[] $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return PetLike[]
     */
    public function getPetLikes(): array
    {
        return $this->petLikes;
    }

    /**
     * @param PetLike[] $petLikes
     */
    public function setPetLikes(array $petLikes): void
    {
        $this->petLikes = $petLikes;
    }

    /**
     * @return Pet[]
     */
    public function getPets(): array
    {
        return $this->pets;
    }

    /**
     * @param Pet[] $pets
     */
    public function setPets(array $pets): void
    {
        $this->pets = $pets;
    }
}
