<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Message;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Helpers\AuthenticationHelper;
use Tests6\DataFixtures\ORM\UserFixture;

/**
 * @group functional
 */
final class ReadMessageTest extends WebTestCase
{
    use FixturesTrait;

    private const POST_PETS_CHAT_URL = '/api/v1/user/%s/chat/read';

    private const MESSAGE = 'How U Doing?';

    public function testUserChat(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixtureWithNoMessages::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_POST,
            sprintf(self::POST_PETS_CHAT_URL, UserFixtureWithNoMessages::SLUG_USER_WITH_NO_MESSAGE)
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
//        $content = json_decode($response->getContent(), true);
    }
}
