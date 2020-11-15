<?php

namespace App\PetDomain\VO;

class Id
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function create(string $uuid): self
    {
        return new static($uuid);
    }

    public function __toString(): string
    {
        return $this->uuid;
    }
}
