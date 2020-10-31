<?php

namespace Tests\Unit\Controller\Cat;

use App\Controller\Cat\CatBreedController;
use App\PetDomain\Cat\CatBreedList;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \App\Controller\Cat\CatBreedController
 */
final class CatBreedControllerTest extends TestCase
{
    public function testGetBreedList(): void
    {
        $catBreedController = new CatBreedController();
        $result = json_decode($catBreedController->getBreedList()->getContent(), true);
        self::assertSame(CatBreedList::getAll(), $result);
    }
}
