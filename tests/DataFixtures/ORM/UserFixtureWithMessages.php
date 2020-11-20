<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\User;
use App\Entity\UserMessage;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Message;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

final class UserFixtureWithMessages extends BaseFixture
{
    public const ID_USER_TO_MESSAGE = 'user-to-message';
    public const SLUG_USER_TO_MESSAGE = 'slug-user-to-message';
    public const FIRST_MESSAGE = 'Hello!';
    public const SECOND_MESSAGE = 'Hi!';

    protected function loadData(ObjectManager $manager): void
    {
        /** @var User $userFrom */
        $userFrom = $manager->getRepository(User::class)->find(UserFixture::ID);
        $userTo = new User(self::ID_USER_TO_MESSAGE, self::SLUG_USER_TO_MESSAGE);
        $userMessageFirst = new UserMessage(
            Id::create(Uuid::uuid4()->toString()),
            $userFrom,
            $userTo,
            Message::create(self::FIRST_MESSAGE)
        );
        $userMessageSecond = new UserMessage(
            Id::create(Uuid::uuid4()->toString()),
            $userTo,
            $userFrom,
            Message::create(self::SECOND_MESSAGE)
        );
        $manager->persist($userFrom);
        $manager->persist($userTo);
        $manager->persist($userMessageFirst);
        $manager->flush();
        sleep(1);
        $manager->persist($userMessageSecond);
        $manager->flush();
    }
}
