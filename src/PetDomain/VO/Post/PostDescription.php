<?php

namespace App\PetDomain\VO\Post;

class PostDescription
{
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
