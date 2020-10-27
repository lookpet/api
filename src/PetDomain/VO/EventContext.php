<?php

namespace App\PetDomain\VO;

use App\Entity\Pet;

final class EventContext
{
    private array $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    public static function create(array $context = []): self
    {
        return new static($context);
    }

    public static function createByPet(Pet $pet): self
    {
        return new static([
            'pet' => $pet->getId(),
        ]);
    }

    public function get(): array
    {
        return $this->context;
    }
}
