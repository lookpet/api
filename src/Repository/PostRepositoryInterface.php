<?php

namespace App\Repository;

use App\Dto\Post\PostDto;
use App\Entity\Post;

interface PostRepositoryInterface
{
    public function createFromDto(PostDto $postDto): Post;
}
