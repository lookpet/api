<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\Pet;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

final class UserFixtureWithNoPet extends BaseFixture
{
    public const PET_SLUG = 'pet-slug';
    public const ID_USER_WITH_NO_PET = 'user-with-no-pet';
    public const SLUG_USER_WITH_NO_PET = 'slug-user-with-no-pet';
    public const ID_USER_WITH_PET = 'user-with-pet';
    public const SLUG_USER_WITH_PET = 'slug-user-with-pet';

    protected function loadData(ObjectManager $manager): void
    {
        $userWithNoPet = new User(self::ID_USER_WITH_NO_PET, self::SLUG_USER_WITH_NO_PET);
        $userWithPet = new User(self::ID_USER_WITH_PET, self::SLUG_USER_WITH_PET);
        $pet = new Pet('dog', self::PET_SLUG, null, null, $userWithPet);
        $manager->persist($userWithPet);
        $manager->persist($pet);
        $manager->persist($userWithNoPet);
        $manager->flush();
    }
}
