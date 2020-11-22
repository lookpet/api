<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface UserResponseBuilderInterface
{
    /**
     * @param User $user
     * @param User|null $authenticatedUser
     *
     * @return JsonResponse
     */
    public function buildForOneUser(User $user, ?User $authenticatedUser): JsonResponse;
}
