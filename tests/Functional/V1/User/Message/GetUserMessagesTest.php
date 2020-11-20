<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Message;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\DataFixtures\ORM\UserFixtureWithMessages;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
final class GetUserMessagesTest extends WebTestCase
{
    use FixturesTrait;

    private const GET_PETS_CHAT_URL = '/api/v1/user/%s/chat';

    public function testGetUserChatMessages(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class, UserFixtureWithMessages::class]);

        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            sprintf(self::GET_PETS_CHAT_URL, UserFixtureWithMessages::SLUG_USER_TO_MESSAGE)
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $firstMessage = array_shift($content);
        self::assertSame(UserFixtureWithMessages::FIRST_MESSAGE, $firstMessage['message']);
        self::assertSame(UserFixture::SLUG, $firstMessage['from']['slug']);
        self::assertSame(UserFixtureWithMessages::SLUG_USER_TO_MESSAGE, $firstMessage['to']['slug']);
        $secondMessage = array_shift($content);
        self::assertSame(UserFixtureWithMessages::SECOND_MESSAGE, $secondMessage['message']);
        self::assertSame(UserFixtureWithMessages::SLUG_USER_TO_MESSAGE, $secondMessage['from']['slug']);
        self::assertSame(UserFixture::SLUG, $secondMessage['to']['slug']);
    }
}
