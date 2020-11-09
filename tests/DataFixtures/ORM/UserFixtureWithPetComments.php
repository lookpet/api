<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\Pet;
use App\Entity\PetComment;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

final class UserFixtureWithPetComments extends BaseFixture
{
    public const COMMENT = 'Super comment';
    public const ID_USER_WITH_PET = 'user-with-pet';
    public const SLUG_USER_WITH_PET = 'slug-user-with-pet';
    public const ID_USER_WITH_COMMENT = 'user-with-comment';
    public const SLUG_USER_WITH_COMMENT = 'slug-user-with-comment';

    protected function loadData(ObjectManager $manager): void
    {
        $userWithPet = new User(self::ID_USER_WITH_PET, self::SLUG_USER_WITH_PET);
        $pet = new Pet('dog', self::SLUG, null, null, $userWithPet);
        $userWithComment = new User(self::ID_USER_WITH_COMMENT, self::SLUG_USER_WITH_COMMENT);
        $comment = new PetComment($userWithComment, self::COMMENT, $pet);

        $manager->persist($userWithPet);
        $manager->persist($pet);
        $manager->persist($userWithComment);
        $manager->persist($comment);
        $manager->flush();
    }
}
