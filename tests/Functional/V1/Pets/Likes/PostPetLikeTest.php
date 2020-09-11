<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Pets\Likes;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\PetFixture;
use Tests\DataFixtures\ORM\UserFixture;
use tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
final class PostPetLikeTest extends WebTestCase
{
    use FixturesTrait;

    private const POST_PETS_LIKE_URL = '/api/v1/pet/%s/like';

    public function testPostLike(): void
    {
        $client = static::createClient();
        $this->loadFixtures([PetFixture::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasLike']);
        self::assertSame(1, $content['total']);
    }

    public function testItSwitchesLike(): void
    {
        $client = static::createClient();
        $this->loadFixtures([PetFixture::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasLike']);
        self::assertSame(1, $content['total']);
        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertFalse($content['hasLike']);
        self::assertSame(0, $content['total']);
        $content = $this->makeRequest($client, Response::HTTP_OK);
        self::assertTrue($content['hasLike']);
        self::assertSame(1, $content['total']);
    }

    private function makeRequest(KernelBrowser $client, int $expectedStatusCode): array
    {
        $client->request(
            Request::METHOD_POST,
            sprintf(self::POST_PETS_LIKE_URL, PetFixture::SLUG)
        );
        $response = $client->getResponse();
        self::assertSame($expectedStatusCode, $response->getStatusCode());

        return json_decode($response->getContent(), true);
    }
}
