<?php

namespace App\PetDomain\VO;

use App\Entity\Media;
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

    public static function createByMedia(Media ...$mediaCollection): self
    {
        $result = [];
        foreach ($mediaCollection as $media) {
            $result[] = $media->getId();
        }

        return new static([
            'media' => $result,
        ]);
    }

    public function get(): array
    {
        return $this->context;
    }

    public function __toString(): string
    {
        return json_encode($this->context);
    }
}
