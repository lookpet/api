<?php

namespace App\Tests;

use GuzzleHttp\Client;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
final class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;
    private const REGISTER_URL = '/api/v1/authentication/register';

    public function testSuccessRegister(): void
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'email' => 'igor111@look.pet',
                'password' => '1234',
            ])
        );
        $response = $client->getResponse();

//        $client = new Client([
//            'headers' => ['Content-Type' => 'application/json'],
//            'base_uri' => getenv('API_ENDPOINT_BASE_URL'),
//        ]);

//        $response = $client->request(Request::METHOD_POST,
//            self::REGISTER_URL, [
//            'body' => (string) json_encode([
//                    'email' => 'igor111@look.pet',
//                    'password' => '1234',
//            ]),
//        ]);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
