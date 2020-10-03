<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Pets\Comments;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\PetFixture;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
final class PostPetCommentsTest extends WebTestCase
{
    use FixturesTrait;

    private const POST_PETS_COMMENTS_URL = '/api/v1/pet/%s/comment';

    public function testPostComment(): void
    {
        $client = static::createClient();
        $this->loadFixtures([PetFixture::class, UserFixture::class]);

        $client = AuthenticationHelper::login($client);

        $this->makeRequest($client, Response::HTTP_OK);
    }

    private function makeRequest(KernelBrowser $client, int $expectedStatusCode): array
    {
        $client->request(
            Request::METHOD_POST,
            sprintf(self::POST_PETS_COMMENTS_URL, PetFixture::SLUG),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'comment' => 'some text',
            ])
        );

        $response = $client->getResponse();
        self::assertSame($expectedStatusCode, $response->getStatusCode());

        return json_decode($response->getContent(), true);
    }
}
