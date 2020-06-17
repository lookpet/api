<?php

declare(strict_types=1);

namespace App\PetDomain;

final class PetTypes
{
    public const DOG = 'dog';
    public const CAT = 'cat';

    public static function getList(): array
    {
        return [
            self::DOG => 'собака',
            self::CAT => 'кот/кошка',
        ];
    }
}
