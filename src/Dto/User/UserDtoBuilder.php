<?php

namespace App\Dto\User;

use App\Entity\Breeder;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class UserDtoBuilder implements UserDtoBuilderInterface
{
    private Slugify $slugify;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Slugify $slugify
    ) {
        $this->slugify = $slugify;
        $this->entityManager = $entityManager;
    }

    public function build(Request $request): UserDto
    {
        $userDto = new UserDto();

        if ($request->request->has('firstName')) {
            $userDto->setFirstName($request->request->get('firstName'));
        }

        if ($request->request->has('lastName')) {
            $userDto->setLastName($request->request->get('lastName'));
        }

        if ($request->request->has('phone')) {
            $userDto->setPhone($request->request->get('phone'));
        }

        if ($request->request->has('description')) {
            $userDto->setDescription($request->request->get('description'));
        }

        if ($request->request->has('city')) {
            $userDto->setCity($request->request->get('city'));
        }

        if ($request->request->has('slug')) {
            $userDto->setSlug($request->request->get('slug'));
        } else {
            $this->generateSlug($userDto);
        }

        if ($request->request->has('breeder')) {
            $breeder = new Breeder($request->request->get('breeder'));
            $this->entityManager->persist($breeder);
            $userDto->setBreeder($breeder);
        }

        return $userDto;
    }

    private function generateSlug(UserDto $userDto): void
    {
        $firstName = mb_strtolower($userDto->getFirstName());
        $slugEntropy = base_convert(rand(1000000000, PHP_INT_MAX), 10, 36);
        $userDto->setSlug(
            $this->slugify->slugify(implode('-', [$firstName, $slugEntropy]))
        );
    }
}
