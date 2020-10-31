<?php

namespace App\Dto\Post;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface PostDtoBuilderInterface
{
    public function build(Request $request, User $user): PostDto;
}
