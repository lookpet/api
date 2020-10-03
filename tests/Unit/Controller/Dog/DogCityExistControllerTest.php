<?php

namespace Tests\Unit\Controller\Dog;

use App\Controller\Dog\DogCityExistController;
use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DogCityExistControllerTest extends TestCase
{
    private const BREED_RESPONSE = [
        ['breed' => 'Siberian Husky'],
    ];

    public function testGetDogExistCitiesList(): void
    {
        $dogBreedController = new DogCityExistController();
        $petRepository = $this->createMock(PetRepositoryInterface::class);
        $petRepository
            ->expects(self::once())
            ->method('getExistCities')
            ->with(PetTypes::DOG)
            ->willReturn(self::BREED_RESPONSE);

        $result = json_decode($dogBreedController->getCitiesList($petRepository)->getContent(), true);
        self::assertSame(self::BREED_RESPONSE, $result);
    }
}
