<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Message;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\DataFixtures\ORM\UserFixtureWithMultipleMessages;
use Tests\DataFixtures\ORM\UserFixtureWithNoMessages;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 * @group chat
 */
final class GetChatListHashTest extends WebTestCase
{
    use FixturesTrait;

    private const GET_CHAT_LIST_HASH_URL = '/api/v1/user/chat/list/hash';
    private const ZERO_MESSAGES_RESULT = 0;
    private const THREE_MESSAGES_RESULT = 3;

    public function testItReturnsHashWhenNoChatsExist(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithNoMessages::class]);

        $client = AuthenticationHelper::login($client);
        $client->request(
            Request::METHOD_GET,
            self::GET_CHAT_LIST_HASH_URL
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertSame(
            sha1((string) self::ZERO_MESSAGES_RESULT), $content
        );
    }

    public function testItReturnsHashWhenChatsExist(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithMultipleMessages::class]);

        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            self::GET_CHAT_LIST_HASH_URL
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertSame(
            sha1((string) self::THREE_MESSAGES_RESULT), $content
        );
    }
}
