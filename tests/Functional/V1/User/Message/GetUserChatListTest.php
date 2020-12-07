<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Message;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\DataFixtures\ORM\UserFixtureWithMultipleMessages;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
final class GetUserChatListTest extends WebTestCase
{
    use FixturesTrait;

    private const GET_USER_CHAT_URL = '/api/v1/user/chat/list';

    public function testGetUserLastChatMessages(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithMultipleMessages::class]);

        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            self::GET_USER_CHAT_URL
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $firstMessage = array_shift($content);
        self::assertSame(UserFixtureWithMultipleMessages::THIRD_MESSAGE, $firstMessage['message']);
        self::assertSame(UserFixture::SLUG, $firstMessage['from']['slug']);
        self::assertSame(UserFixtureWithMultipleMessages::SLUG_ANOTHER_USER_TO_MESSAGE, $firstMessage['to']['slug']);
        $secondMessage = array_shift($content);
        self::assertSame(UserFixtureWithMultipleMessages::SECOND_MESSAGE, $secondMessage['message']);
        self::assertSame(UserFixtureWithMultipleMessages::SLUG_USER_TO_MESSAGE, $secondMessage['from']['slug']);
        self::assertSame(UserFixture::SLUG, $secondMessage['to']['slug']);
    }
}
