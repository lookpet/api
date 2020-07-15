<?php

namespace App\PetDomain\VO;

class FilePath implements \JsonSerializable
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function __toString(): string
    {
        return $this->filePath;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
