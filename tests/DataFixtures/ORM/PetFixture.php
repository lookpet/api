<?php

namespace Tests\DataFixtures\ORM;

use App\Dto\Pet\PetDto;
use App\Entity\Pet;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class PetFixture extends BaseFixture implements DependentFixtureInterface
{
    public const SLUG = 'feodor';

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /** {@inheritdoc} */
    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findByEmail(UserFixture::TEST_USER_EMAIL);

        $this->faker = Factory::create();
        $pet = new Pet('dog', self::SLUG, Uuid::uuid4()->toString(), $this->faker->firstName);
        $petDto = new PetDto();
        $petDto->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
        $petDto->setBreed('Husky');
        $petDto->setColor('Black');
        $petDto->setEyeColor('Blue');
        $pet->updateFromDto($petDto);
        $pet->setUser($user);

        $manager->persist($pet);
        $manager->flush();
    }

    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(100, 'pets', function ($i) {
            $pet = new Pet('dog', $this->faker->slug, Uuid::uuid4()->toString(), $this->faker->name);
            $petDto = new PetDto();
            $petDto->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
            $petDto->setBreed('Husky');
            $petDto->setColor('Black');
            $petDto->setEyeColor('Blue');
            $pet->updateFromDto($petDto);

            return $pet;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
