<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

final class UserFixtureWithFollowers extends BaseFixture
{
    public const ID_USER_WITH_NO_FOLLOWER = 'user-with-no-follower';
    public const SLUG_USER_WITH_NO_FOLLOWER = 'slug-user-with-no-follower';

    protected function loadData(ObjectManager $manager): void
    {
        $userWithNoFollower = new User(self::ID_USER_WITH_NO_FOLLOWER, self::SLUG_USER_WITH_NO_FOLLOWER);
        $manager->persist($userWithNoFollower);
        $manager->flush();
    }
}
