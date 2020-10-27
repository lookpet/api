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
     * @return null|PetLike
     */
    public function getUserPetLike(User $user, Pet $pet): ?PetLike;
}
