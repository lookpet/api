<?php

namespace Tests\Unit\Controller\Dog;

use App\Controller\Dog\DogBreedController;
use App\PetDomain\Dog\DogBreedList;
use PHPUnit\Framework\TestCase;

class DogBreedControllerTest extends TestCase
{
    public function testGetDogBreedList(): void
    {
        $dogBreedController = new DogBreedController();
        $result = json_decode($dogBreedController->getBreedList()->getContent(), true);
        self::assertSame(DogBreedList::getAll(), $result);
    }
}
