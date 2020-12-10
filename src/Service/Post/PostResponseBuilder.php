<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

final class PostResponseBuilder implements PostResponseBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(?UserInterface $user, Post ...$posts): JsonResponse
    {
        $result = [];

        if (count($posts) !== 0) {
            foreach ($posts as $pet) {
                $result[] = array_merge(
                    $pet->jsonSerialize(),
                    [
                        'hasLike' => $pet->hasLike($user),
                    ]
                );
            }
        }

        return new JsonResponse([
            'pets' => $result,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForSinglePost(Post $post, ?UserInterface $user): JsonResponse
    {
        return new JsonResponse(array_merge(
            $post->jsonSerialize(),
            [
                'hasLike' => $post->hasLike($user),
                'age' => $this->ageCalculator->getAge($post->getDateOfBirth()),
            ]
        ));
    }
}
