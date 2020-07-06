<?php

namespace App\PetDomain\VO;

class Gender implements \JsonSerializable
{
    public const ALL = [
        'male', 'female',
    ];
    private string $gender;

    public function __construct(string $gender)
    {
        $this->gender = $gender;
    }

    public function __toString(): string
    {
        return $this->gender;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
