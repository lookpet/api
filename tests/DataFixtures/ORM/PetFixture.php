<?php

namespace tests\DataFixtures\ORM;

use App\Entity\Pet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class PetFixture extends BaseFixture
{
    public function loadData(ObjectManager $manager):void
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
