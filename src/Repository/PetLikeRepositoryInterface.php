<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Entity\PetLike;
use App\Entity\User;

interface PetLikeRepositoryInterface
{
    /**
     * @param User $user
     * @param Pet $pet
     *
     * @return PetLike[]
     */
    public function getPetLikes(User $user, Pet $pet): array;
}
