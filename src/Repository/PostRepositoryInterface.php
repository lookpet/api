<?php

namespace App\Repository;

use App\Dto\Post\PostDto;
use App\Entity\Post;
use App\Entity\User;
use App\PetDomain\VO\Offset;

interface PostRepositoryInterface
{
    /**
     * @param PostDto $postDto
     *
     * @return Post
     */
    public function createFromDto(PostDto $postDto): Post;

    /**
     * @param User|null $user
     * @param Offset|null $offset
     *
     * @return Post[]|iterable
     */
    public function getFeed(Offset $offset, ?User $user = null): iterable;
}
