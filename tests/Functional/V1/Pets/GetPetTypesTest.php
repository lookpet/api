<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Pets;

use App\PetDomain\PetTypes;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
final class GetPetTypesTest extends WebTestCase
{
    private const GET_PET_TYPES_URL = '/api/v1/types';

    public function testGetPetTypes(): void
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_GET,
            self::GET_PET_TYPES_URL
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(PetTypes::getList(), $content);
    }
}
