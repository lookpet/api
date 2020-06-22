<?php

namespace App\Service;

use App\Entity\Pet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface PetResponseBuilderInterface
{
    public function build(?UserInterface $user, Pet ...$pets): JsonResponse;
}
