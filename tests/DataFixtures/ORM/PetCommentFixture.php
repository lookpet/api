<?php

namespace Tests\DataFixtures\ORM;

use App\Dto\Pet\PetDto;
use App\Entity\Pet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class PetCommentFixture extends Fixture
{
    public const SLUG = 'feodor';

    /** {@inheritdoc} */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $pet = new Pet('dog', self::SLUG, Uuid::uuid4()->toString(), $this->faker->firstName);
        $petDto = new PetDto();
        $petDto->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
        $petDto->setBreed('Husky');
        $petDto->setColor('Black');
        $petDto->setEyeColor('Blue');
        $pet->updateFromDto($petDto);

        $manager->persist($pet);
        $manager->flush();
    }
}
