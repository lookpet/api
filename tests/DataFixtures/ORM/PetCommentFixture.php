<?php

namespace tests\DataFixtures\ORM;

use App\Entity\Pet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class PetCommentFixture extends Fixture
{
    public const SLUG = 'feodor';

    /** {@inheritdoc} */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $pet = new Pet('dog', self::SLUG, $this->faker->firstName);
        $pet->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
        $pet->setBreed('Husky');
        $pet->setColor('Black');
        $pet->setEyeColor('Blue');

        $manager->persist($pet);
        $manager->flush();
    }
}
