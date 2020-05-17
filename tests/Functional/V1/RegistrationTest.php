<?php

declare(strict_types=1);


namespace Functional\V1;


use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use tests\DataFixtures\ORM\UserFixture;

/**
 * @group functional
 * @IgnoreAnnotation("dataProvider")
 */
final class RegistrationTest extends WebTestCase
{
    use FixturesTrait;
    private const REGISTER_URL = '/api/v1/authentication/register';

    private const TEST_EMAIL = 'igor@look.pet';

    public function testRegistrationSuccess(): void
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
                'email' => self::TEST_EMAIL,
                'password' => '1234',
            ])
        );
        $response = $client->getResponse();

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRegistrationFailsBecauseUserWithSameEmailExists(): void
    {
        $client = static::createClient();
        $this->loadFixtures([
            UserFixture::class
        ]);

        $client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'email' => UserFixture::TEST_USER_EMAIL,
                'password' => '1234',
            ])
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals('User already exist', $content['message']);
    }

    /**
     * @dataProvider dataTestRegistrationFailsBecauseInputDataIsNotSet
     *
     * @param array $requestData
     * @param string $responseMessage
     */
    public function testRegistrationFailsBecauseInputDataIsNotSet(array $requestData, string $responseMessage): void
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode($requestData)
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals($responseMessage, $content['message']);
    }

    public function dataTestRegistrationFailsBecauseInputDataIsNotSet(): array
    {
        return [
            [
                [
                    'password' => UserFixture::DEFAULT_PASSWORD,
                ],
                'Empty email'
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_EMAIL,
                ],
                'Empty password'
            ]
        ];
    }
}