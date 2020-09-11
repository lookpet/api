<?php

namespace App\PetDomain\VO;

class Url implements \JsonSerializable
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function __toString(): string
    {
        return $this->url;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
