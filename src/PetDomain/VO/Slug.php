<?php

namespace App\PetDomain\VO;

class Slug
{
    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function __toString(): string
    {
        return $this->slug;
    }
}
