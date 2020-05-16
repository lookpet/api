<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 */
class PetControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testGetPet()
    {
        $this->loadFixtures();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/v1/pet/fedor');

        self::assertResponseIsSuccessful();
    }
}
