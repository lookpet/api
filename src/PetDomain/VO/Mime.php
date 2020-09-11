<?php

namespace App\PetDomain\VO;

class Mime implements \JsonSerializable
{
    private string $mime;

    public function __construct(string $mime)
    {
        $this->mime = $mime;
    }

    public function __toString(): string
    {
        return $this->mime;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
