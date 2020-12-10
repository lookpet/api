<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface PostResponseBuilderInterface
{
    /**
     * @param UserInterface|null $user
     * @param Post ...$posts
     *
     * @return JsonResponse
     */
    public function build(?UserInterface $user, Post ...$posts): JsonResponse;

    /**
     * @param Post $post
     * @param UserInterface|null $user
     *
     * @return JsonResponse
     */
    public function buildForSinglePost(Post $post, ?UserInterface $user): JsonResponse;
}
