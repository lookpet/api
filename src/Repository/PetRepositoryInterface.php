<?php

namespace App\Repository;

interface PetRepositoryInterface
{
    public function getExistBreeds($petType): iterable;

    public function getExistCities($petType): iterable;
}
