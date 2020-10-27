<?php

namespace Tests\Unit\Controller\Pet;

use App\Controller\Pet\PetTypeController;
use App\PetDomain\PetTypes;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \App\Controller\Pet\PetTypeController
 */
class PetTypesTest extends TestCase
{
    public function testItGetTypes(): void
    {
        $petTypeController = new PetTypeController();
        $result = json_decode(
            $petTypeController->getTypes()->getContent(),
            true
        );

        self::assertSame(
            PetTypes::getList(),
            $result
        );
    }
}
