<?php

namespace App\PetDomain\VO;

class Height implements \JsonSerializable
{
    private string $height;

    public function __construct(string $height)
    {
        $this->height = $height;
    }

    public function __toString(): string
    {
        return $this->height;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }

    public function get(): float
    {
        return floatval($this->height);
    }
}
