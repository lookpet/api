<?php

namespace tests\DataFixtures\ORM;

use App\Entity\Pet;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class PetFixture extends BaseFixture
{
    /** {@inheritdoc} */
//    public function load(ObjectManager $manager): void
//    {
//        $this->faker = Factory::create();
//        $pet = new Pet('dog', $this->faker->slug, $this->faker->firstName);
//        $pet->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
//        $pet->setBreed('Husky');
//        $pet->setColor('Black');
//        $pet->setEyeColor('Blue');
//        $manager->persist($pet);
//        $manager->flush();
//    }

    public function loadData(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $this->createMany(100, 'pets', function ($i) {
            $pet = new Pet('dog', $this->faker->slug, $this->faker->name);
            $pet->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $this->faker->date()));
            $pet->setBreed('Husky');
            $pet->setColor('Black');
            $pet->setEyeColor('Blue');

            return $pet;
        });

        $manager->flush();
    }
}
