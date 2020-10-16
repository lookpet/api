<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Cat;

use App\PetDomain\Cat\CatBreedList;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 * @group cat
 */
final class GetCatBreedsTest extends WebTestCase
{
    private const GET_CAT_BREEDS_URL = '/api/v1/cat/breeds';

    public function testGetBreeds(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_GET,
            self::GET_CAT_BREEDS_URL
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(CatBreedList::getAll(), $content);
    }
}
