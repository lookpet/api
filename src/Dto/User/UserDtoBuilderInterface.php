<?php

namespace App\Dto\User;

use Symfony\Component\HttpFoundation\Request;

interface UserDtoBuilderInterface
{
    public function build(Request $request): UserDto;
}
