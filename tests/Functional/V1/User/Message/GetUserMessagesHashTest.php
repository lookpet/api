<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Message;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\DataFixtures\ORM\UserFixtureWithMessages;
use Tests\DataFixtures\ORM\UserFixtureWithNoMessages;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 * @group chat
 */
final class GetUserMessagesHashTest extends WebTestCase
{
    use FixturesTrait;

    private const GET_USER_MESSAGES_CHAT_HASH_URL = '/api/v1/user/%s/chat/hash';
    private const ZERO_MESSAGES_RESULT = 0;
    private const TWO_MESSAGES_RESULT = 2;

    public function testItReturnsHashWhenNoMessagesExist(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithNoMessages::class]);

        $client = AuthenticationHelper::login($client);
        $client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_MESSAGES_CHAT_HASH_URL, UserFixtureWithNoMessages::SLUG_USER_WITH_NO_MESSAGE)
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertSame(
            sha1((string) self::ZERO_MESSAGES_RESULT), $content
        );
    }

    public function testItReturnsHashWhenMessagesExist(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithMessages::class]);

        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_MESSAGES_CHAT_HASH_URL, UserFixtureWithMessages::SLUG_USER_TO_MESSAGE)
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertSame(
            sha1((string) self::TWO_MESSAGES_RESULT), $content
        );
    }
}
