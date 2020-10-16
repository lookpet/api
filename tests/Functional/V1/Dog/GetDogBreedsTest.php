<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Dog;

use App\PetDomain\Dog\DogBreedList;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 * @group dog
 */
final class GetDogBreedsTest extends WebTestCase
{
    private const GET_DOG_BREEDS_URL = '/api/v1/dog/breeds';

    public function testGetBreeds(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_GET,
            self::GET_DOG_BREEDS_URL
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(DogBreedList::getAll(), $content);
    }
}
