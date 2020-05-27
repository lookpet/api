<?php

declare(strict_types=1);


namespace App\PetDomain;


final class Genders
{
    private const MALE = 'male';
    private const FEMALE = 'female';

    public static function getAll(): array
    {
        return [
            self::FEMALE,
            self::MALE,
        ];
    }
}