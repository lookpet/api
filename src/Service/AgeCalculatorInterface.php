<?php

namespace App\Service;

interface AgeCalculatorInterface
{
    public function getAge(?\DateTimeInterface $dateTime): ?string;
}
