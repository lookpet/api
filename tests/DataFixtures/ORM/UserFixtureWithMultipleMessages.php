<?php

declare(strict_types=1);

namespace Tests\DataFixtures\ORM;

use App\Entity\User;
use App\Entity\UserMessage;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Message;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

final class UserFixtureWithMultipleMessages extends BaseFixture
{
    public const ID_USER_TO_MESSAGE = 'user-to-message';
    public const SLUG_USER_TO_MESSAGE = 'slug-user-to-message';
    public const ID_ANOTHER_USER_TO_MESSAGE = 'another-user-to-message';
    public const SLUG_ANOTHER_USER_TO_MESSAGE = 'another-slug-user-to-message';
    public const FIRST_MESSAGE = 'Hello!';
    public const SECOND_MESSAGE = 'Hi!';
    public const THIRD_MESSAGE = 'Howdy!';

    protected function loadData(ObjectManager $manager): void
    {
        /** @var User $userFrom */
        $userFrom = $manager->getRepository(User::class)->find(UserFixture::ID);
        $userTo = new User(self::ID_USER_TO_MESSAGE, self::SLUG_USER_TO_MESSAGE);
        $anotherUserTo = new User(self::ID_ANOTHER_USER_TO_MESSAGE, self::SLUG_ANOTHER_USER_TO_MESSAGE);
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

        $userMessageThird = new UserMessage(
            Id::create(Uuid::uuid4()->toString()),
            $userFrom,
            $anotherUserTo,
            Message::create(self::THIRD_MESSAGE)
        );

        $manager->persist($userFrom);
        $manager->persist($userTo);
        $manager->persist($anotherUserTo);
        $manager->persist($userMessageFirst);
        $manager->flush();
        sleep(1);
        $manager->persist($userMessageSecond);
        sleep(1);
        $manager->persist($userMessageThird);
        $manager->flush();
    }
}
