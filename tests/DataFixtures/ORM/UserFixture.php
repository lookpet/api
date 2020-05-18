<?php

namespace tests\DataFixtures\ORM;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixture extends BaseFixture
{
    public const TEST_USER_FIRST_NAME = 'Igor';
    public const DEFAULT_PASSWORD = 'engage';
    public const TEST_USER_EMAIL = 'igor@look.pet';

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
        $user = new User();
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            self::DEFAULT_PASSWORD
        ));
        $user->setFirstName(self::TEST_USER_FIRST_NAME);
        $manager->persist($user);
        $manager->flush();
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'main_users', function ($i) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('user%d@look.pet', $i));
            $user->setFirstName($this->faker->firstName);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                self::DEFAULT_PASSWORD
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
