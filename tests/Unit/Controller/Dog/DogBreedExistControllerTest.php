<?php

namespace Tests\Unit\Controller\Dog;

use App\Controller\Dog\DogBreedExistController;
use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DogBreedExistControllerTest extends TestCase
{
    private const BREED_RESPONSE = [
        ['breed' => 'Siberian Husky'],
    ];

    public function testGetExistBreedList(): void
    {
        $dogBreedController = new DogBreedExistController();
        $petRepository = $this->createMock(PetRepositoryInterface::class);
        $petRepository
            ->expects(self::once())
            ->method('getExistBreeds')
            ->with(PetTypes::DOG)
            ->willReturn(self::BREED_RESPONSE);

        $result = json_decode($dogBreedController->getBreedList($petRepository)->getContent(), true);
        self::assertSame(self::BREED_RESPONSE, $result);
    }
}
