<?php

namespace App\Tests;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 */
class SecurityControllerTest extends WebTestCase
{
    public function testRegister(): void
    {
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => getenv('API_ENDPOINT_BASE_URL'),
        ]);

        $response = $client->request('POST',
            '/api/v1/authentication/register', [
            'body' => (string) json_encode([
                    'email' => 'igor1@look.pet',
                    'password' => '1234',
            ]),
        ]);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
