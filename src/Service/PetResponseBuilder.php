<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Pet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

final class PetResponseBuilder
{
    public static function buildResponse(iterable $petsCollection, ?UserInterface $user): JsonResponse
    {
        $result = [];

        if (count($petsCollection) !== 0) {
            foreach ($petsCollection as $pet) {
                $result[] = array_merge(
                    $pet->jsonSerialize(),
                    [
                        'hasLike' => $pet->hasLike($user)
                    ]
                );
            }
        }

        return new JsonResponse([
            'pets' => $result,
        ]);
    }

    public static function buildSingle(Pet $pet, ?UserInterface $user): JsonResponse
    {
        return new JsonResponse(array_merge(
            $pet->jsonSerialize(),
            [
                'hasLike' => $pet->hasLike($user)
            ]
        ));
    }
}