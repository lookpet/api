<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Authentication;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;

/**
 * @group functional
 * @IgnoreAnnotation("dataProvider")
 */
final class LoginTest extends WebTestCase
{
    use FixturesTrait;

    private const LOGIN_URL = '/api/v1/authentication/login';

    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class]);

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

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(UserFixture::TEST_USER_FIRST_NAME, $content['user']['firstName']);
        self::assertNotEmpty($content['token']);
        self::assertNotEmpty($content['expires_at']);
        self::assertEqualsWithDelta(new \DateTimeImmutable('+ 1 week'), new \DateTimeImmutable($content['expires_at']), 1);
//        self::assertSame(3, $content['expires_at']['timezone_type']);
//        self::assertSame('Europe/London', $content['expires_at']['timezone']);
    }

    /**
     * @dataProvider dataTestRegistrationFailsBecauseInputDataIsNotSet
     *
     * @param array $requestData
     * @param string $responseMessage
     * @param int $responseCode
     */
    public function testLoginFailsBecauseInputDataIsNotSet(array $requestData, string $responseMessage, int $responseCode): void
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request(
            Request::METHOD_POST,
            self::LOGIN_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode($requestData)
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals($responseCode, $response->getStatusCode());
        self::assertEquals($responseMessage, $content['message']);
    }

    public function dataTestRegistrationFailsBecauseInputDataIsNotSet(): array
    {
        return [
            [
                [
                    'password' => UserFixture::PASSWORD_GOOD,
                ],
                'Empty email',
                Response::HTTP_FORBIDDEN,
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_EMAIL,
                ],
                'Empty password',
                Response::HTTP_BAD_REQUEST,
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_BAD_EMAIL,
                    'password' => UserFixture::PASSWORD_GOOD,
                ],
                'Invalid email or password',
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }
}
