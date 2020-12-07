<?php

declare(strict_types=1);

namespace Tests\Functional\V1\User\Followers;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\DataFixtures\ORM\UserFixtureWithNoFollowers;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
final class GetUserFollowersTest extends WebTestCase
{
    use FixturesTrait;

    private const POST_PETS_LIKE_URL = '/api/v1/user/%s/follow';

    public function testUserFollow(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixtureWithNoFollowers::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasFollower']);
        self::assertSame(1, $content['total']);
    }

    public function testUserSwitchesFollow(): void
    {
        $client = static::createClient();
        $this->loadFixtures([UserFixtureWithNoFollowers::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasFollower']);
        self::assertSame(1, $content['total']);
        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertFalse($content['hasFollower']);
        self::assertSame(0, $content['total']);
        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasFollower']);
        self::assertSame(1, $content['total']);
    }

    private function makeRequest(KernelBrowser $client, int $expectedStatusCode): array
    {
        $client->request(
            Request::METHOD_POST,
            sprintf(self::POST_PETS_LIKE_URL, UserFixtureWithNoFollowers::SLUG_USER_WITH_NO_FOLLOWER)
        );
        $response = $client->getResponse();
        self::assertSame($expectedStatusCode, $response->getStatusCode());

        return json_decode($response->getContent(), true);
    }
}
