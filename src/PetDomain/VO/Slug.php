<?php

namespace App\PetDomain\VO;

class Slug
{
    private ?string $slug;

    public function __construct(?string $slug = null)
    {
        $this->slug = $slug;
    }
}
