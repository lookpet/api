<?php

namespace Tests\Unit\Controller\Cat;

use App\Controller\Cat\CatBreedExistController;
use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \App\Controller\Cat\CatBreedExistController
 */
final class CatBreedExistControllerTest extends TestCase
{
    private const BREED_RESPONSE = [
        ['breed' => 'Maine Coon'],
    ];

    public function testGetExistBreedList(): void
    {
        $catBreedController = new CatBreedExistController();
        $petRepository = $this->createMock(PetRepositoryInterface::class);
        $petRepository
            ->expects(self::once())
            ->method('getExistBreeds')
            ->with(PetTypes::CAT)
            ->willReturn(self::BREED_RESPONSE);

        $result = json_decode($catBreedController->getBreedList($petRepository)->getContent(), true);
        self::assertSame(self::BREED_RESPONSE, $result);
    }
}
