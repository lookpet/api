<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

final class UserFixtureWithNoMessages extends BaseFixture
{
    public const ID_USER_WITH_NO_MESSAGE = 'user-with-no-message';
    public const SLUG_USER_WITH_NO_MESSAGE = 'slug-user-with-no-message';

    protected function loadData(ObjectManager $manager): void
    {
        $userWithNoFollower = new User(self::ID_USER_WITH_NO_MESSAGE, self::SLUG_USER_WITH_NO_MESSAGE);
        $manager->persist($userWithNoFollower);
        $manager->flush();
    }
}
