<?php

namespace App\Dto\Pet;

use Symfony\Component\HttpFoundation\Request;

interface PetDtoBuilderInterface
{
    public function build(Request $request, ?string $id = null): PetDto;
}
