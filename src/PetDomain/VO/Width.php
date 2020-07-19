<?php

namespace App\PetDomain\VO;

class Width implements \JsonSerializable
{
    private ?string $width;

    public function __construct(?string $width)
    {
        $this->width = $width;
    }

    public function __toString(): ?string
    {
        return $this->width;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }

    public function get():float
    {
        return floatval($this->width);
    }
}
