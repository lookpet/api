<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Symfony\Component\HttpFoundation\Request;
use Tests\DataFixtures\ORM\UserFixture;

final class AuthenticationHelper
{
    private const LOGIN_URL = '/api/v1/authentication/login';

    public static function login($client)
    {
        $client->request(
            Request::METHOD_POST,
            self::LOGIN_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'email' => UserFixture::TEST_USER_EMAIL,
                'password' => UserFixture::PASSWORD_GOOD,
            ])
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', \sprintf('Bearer %s', $content['token']));

        return $client;
    }
}
