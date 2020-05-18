<?php

declare(strict_types=1);

namespace Functional\V1\Pets;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use tests\DataFixtures\ORM\PetFixture;

/**
 * @group functional
 */
final class GetPetsTest extends WebTestCase
{
    use FixturesTrait;

    private const GET_PETS_URL = '/api/v1/pets';

    public function testGetPets(): void
    {
        $client = static::createClient();
        $this->loadFixtures([PetFixture::class]);

        $client->request(
            Request::METHOD_GET,
            self::GET_PETS_URL
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
