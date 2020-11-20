<?php

namespace Tests\DataFixtures\ORM;

use App\Dto\User\UserDto;
use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixture extends BaseFixture
{
    public const TEST_USER_FIRST_NAME = 'Igor';
    public const TEST_USER_LAST_NAME = 'Sinitsyn';
    public const PASSWORD_GOOD = 'engage';
    public const PASSWORD_BAD = '1';
    public const TEST_USER_EMAIL = 'igor@look.pet';
    public const TEST_USER_BAD_EMAIL = 'igor.ru';
    public const SLUG = 'super-pups';
    public const CITY = 'New York';
    public const PLACE_ID = 'super-slug';
    public const PHONE = '+39001234567';
    public const DESCRIPTION = 'My super description!';
    public const ID = 'user-id';

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
        $user = new User(self::ID, self::SLUG);
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            self::PASSWORD_GOOD
        ));
        $userDto = new UserDto();
        $userDto->setFirstName(self::TEST_USER_FIRST_NAME);
        $userDto->setLastName(self::TEST_USER_LAST_NAME);
        $userDto->setSlug(self::SLUG);
        $userDto->setPhone(self::PHONE);
        $userDto->setCity(self::CITY);
        $userDto->setPlaceId(self::PLACE_ID);
        $userDto->setDescription(self::DESCRIPTION);
        $user->updateFromDto($userDto);
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
