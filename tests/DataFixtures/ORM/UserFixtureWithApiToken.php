<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

final class UserFixtureWithApiToken extends BaseFixture
{
    public const ID_USER_WITH_API_TOKEN = 'user-with-token';
    public const SLUG_USER_WITH_API_TOKEN = 'slug-user-with-no-pet';

    protected function loadData(ObjectManager $manager): void
    {
        $userWithToken = new User(self::ID_USER_WITH_API_TOKEN, self::SLUG_USER_WITH_API_TOKEN);
        $apiToken = new ApiToken($userWithToken);
        $manager->persist($apiToken);
        $manager->persist($userWithToken);
        $manager->flush();
    }
}
