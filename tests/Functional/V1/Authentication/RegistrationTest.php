<?php

declare(strict_types=1);

namespace Functional\V1\Authentication;

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
                'firstName' => UserFixture::TEST_USER_FIRST_NAME,
                'email' => UserFixture::TEST_USER_EMAIL,
                'password' => '1234',
            ])
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(UserFixture::TEST_USER_FIRST_NAME, $content['user']['firstName']);
        self::assertNotEmpty($content['token']);
        self::assertNotEmpty($content['expires_at']);
        self::assertEqualsWithDelta(new \DateTimeImmutable('+ 1 week'), new \DateTimeImmutable($content['expires_at']['date']), 1);
        self::assertSame(3, $content['expires_at']['timezone_type']);
        self::assertSame('Europe/London', $content['expires_at']['timezone']);
    }

    public function testRegistrationFailsBecauseUserWithSameEmailExists(): void
    {
        $client = static::createClient();
        $this->loadFixtures([
            UserFixture::class,
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
                'Empty email',
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_EMAIL,
                ],
                'Empty password',
            ],
        ];
    }
}
