<?php

namespace Tests\Unit\Controller\Cat;

use App\Controller\Cat\CatCityExistController;
use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CatCityExistControllerTest extends TestCase
{
    private const BREED_RESPONSE = [
        ['breed' => 'Maine Coon'],
    ];

    public function testGetCatExistCitiesList(): void
    {
        $catBreedController = new CatCityExistController();
        $petRepository = $this->createMock(PetRepositoryInterface::class);
        $petRepository
            ->expects(self::once())
            ->method('getExistCities')
            ->with(PetTypes::CAT)
            ->willReturn(self::BREED_RESPONSE);

        $result = json_decode($catBreedController->getCitiesList($petRepository)->getContent(), true);
        self::assertSame(self::BREED_RESPONSE, $result);
    }
}
