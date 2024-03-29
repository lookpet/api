<?php

namespace App\Service;

use App\Entity\Pet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface PetResponseBuilderInterface
{
    /**
     * @param UserInterface|null $user
     * @param Pet ...$pets
     *
     * @return JsonResponse
     */
    public function build(?UserInterface $user, Pet ...$pets): JsonResponse;

    /**
     * @param Pet $pet
     * @param UserInterface|null $user
     *
     * @return JsonResponse
     */
    public function buildForOnePet(Pet $pet, ?UserInterface $user): JsonResponse;
}
