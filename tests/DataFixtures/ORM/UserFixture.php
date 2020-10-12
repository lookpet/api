<?php

namespace Tests\DataFixtures\ORM;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixture extends BaseFixture
{
    public const TEST_USER_FIRST_NAME = 'Igor';
    public const PASSWORD_GOOD = 'engage';
    public const PASSWORD_BAD = '12345';
    public const TEST_USER_EMAIL = 'igor@look.pet';
    public const TEST_USER_BAD_EMAIL = 'igor.ru';
    public const SLUG = 'super-pups';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User(Uuid::uuid4()->toString(), self::SLUG);
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            self::PASSWORD_GOOD
        ));
        $user->setFirstName(self::TEST_USER_FIRST_NAME);
        $manager->persist($user);
        $manager->flush();
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'main_users', function ($i) use ($manager) {
            $user = new User(Uuid::uuid4()->toString(), self::SLUG . $i);
            $user->setEmail(sprintf('user%d@look.pet', $i));
            $user->setFirstName($this->faker->firstName);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                self::PASSWORD_GOOD
            ));
            $apiToken1 = new ApiToken($user);
            $apiToken2 = new ApiToken($user);
            $manager->persist($apiToken1);
            $manager->persist($apiToken2);

            return $user;
        });

        $manager->flush();
    }
}
