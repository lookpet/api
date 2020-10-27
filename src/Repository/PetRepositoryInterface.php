<?php

namespace App\Repository;

use App\Entity\Pet;
use App\PetDomain\VO\Gender;
use App\PetDomain\VO\Offset;
use App\PetDomain\VO\Slug;

interface PetRepositoryInterface
{
    public function getExistBreeds($petType): iterable;

    public function getExistCities($petType): iterable;

    public function findBySearch(?string $breed, ?string $type, ?string $city, ?bool $isLookingForNewOwner = null, ?Gender $gender = null, ?Offset $offset = null): iterable;

    public function findBySlug(Slug $slug): ?Pet;
}
