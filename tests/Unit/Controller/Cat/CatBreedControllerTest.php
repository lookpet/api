<?php

namespace Tests\Unit\Controller\Cat;

use App\Controller\Cat\CatBreedController;
use App\PetDomain\Cat\CatBreedList;
use PHPUnit\Framework\TestCase;

class CatBreedControllerTest extends TestCase
{
    public function testGetBreedList(): void
    {
        $catBreedController = new CatBreedController();
        $result = json_decode($catBreedController->getBreedList()->getContent(), true);
        self::assertSame(CatBreedList::getAll(), $result);
    }
}
