<?php

declare(strict_types=1);

namespace App\PetDomain;

final class PetTypes
{
    public static function getList(): array
    {
        return [
            'dog' => 'собака',
            'cat' => 'кот/кошка',
        ];
    }
}
