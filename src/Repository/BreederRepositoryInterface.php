<?php

namespace App\Repository;

use App\Entity\Breeder;

interface BreederRepositoryInterface
{
    public function findByName(string $name): ?Breeder;
}
