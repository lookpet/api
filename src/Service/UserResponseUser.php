<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserResponseUser implements UserResponseBuilderInterface
{
    public function buildForOneUser(User $user, ?User $authenticatedUser): JsonResponse
    {
        return new JsonResponse(array_merge(
            $user->jsonSerialize(),
            [
                'hasLike' => $user->hasFollower($authenticatedUser),
            ]
        ));
    }
}
