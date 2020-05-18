<?php

declare(strict_types=1);


namespace App\Dto;


final class PetDto
{
    private string $type;
    private ?string $slug;
    private ?string $name;
    private ?string $breed;
    private ?string $color;
    private ?string $eyeColor;
    private ?string $dateOfBirth;
    private ?string $gender;
    private ?string $about;
    private bool $isLookingForNewOwner=false;

}