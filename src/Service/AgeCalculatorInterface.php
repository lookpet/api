<?php

namespace App\Service;

use App\Entity\Pet;

interface AgeCalculatorInterface
{
    public function getAge(Pet $pet): ?string;
}