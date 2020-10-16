<?php

namespace Tests\Functional\V1\User;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
class UpdateUserInfoTest extends WebTestCase
{
    use FixturesTrait;

    private const UPDATE_USER_URL = '/api/v1/user';
    private const GET_USER_URL = '/api/v1/user/%s';
    private const PHOTO_URL = __DIR__ . '/../../../DataFixtures/Media/photo.jpg';

    private const FIRST_NAME = 'Филипп Филиппович';
    private const LAST_NAME = 'Преображенский';
    private const SLUG = 'phil';
    private const PHONE = '+79037778899';
    private const DESCRIPTION = 'Hello world!';
    private const CITY = 'Москва';
    private const PLACE_ID = 'moscow';

    public function testItUpdatesUserPhoto(): void
    {
        /** @var KernelBrowser $client */
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, UserFixture::SLUG)
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertSame(null, $content['avatar']);
        self::assertEmpty($content['media']);

        $client->request(
            Request::METHOD_POST,
            self::UPDATE_USER_URL,
            [],
            [
                'photo' => new UploadedFile(
                    self::PHOTO_URL,
                    'test'
                ),
            ]
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertNotEmpty($content['avatar']);
        self::assertCount(1, $content['media']);
        self::assertSame('1080', $content['media'][0]['width']);
        self::assertSame('1080', $content['media'][0]['height']);
        self::assertSame($content['avatar'], $content['media'][0]['publicUrl']);
    }

    public function testItUpdatesUserInformation(): void
    {
        /** @var KernelBrowser $client */
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $client = AuthenticationHelper::login($client);

        $client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, UserFixture::SLUG)
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertSame(UserFixture::TEST_USER_FIRST_NAME, $content['firstName']);
        self::assertSame(UserFixture::TEST_USER_LAST_NAME, $content['lastName']);
        self::assertSame(UserFixture::SLUG, $content['slug']);
        self::assertSame(UserFixture::DESCRIPTION, $content['description']);
        self::assertSame(UserFixture::PHONE, $content['phone']);
        self::assertSame(UserFixture::CITY, $content['city']);
        self::assertSame(UserFixture::PLACE_ID, $content['placeId']);

        $client->request(
            Request::METHOD_POST,
            self::UPDATE_USER_URL,
            [
                'firstName' => self::FIRST_NAME,
                'lastName' => self::LAST_NAME,
                'slug' => self::SLUG,
                'description' => self::DESCRIPTION,
                'phone' => self::PHONE,
                'city' => self::CITY,
                'placeId' => self::PLACE_ID,
            ]
        );
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertSame(self::FIRST_NAME, $content['firstName']);
        self::assertSame(self::LAST_NAME, $content['lastName']);
        self::assertSame(self::SLUG, $content['slug']);
        self::assertSame(self::DESCRIPTION, $content['description']);
        self::assertSame(self::PHONE, $content['phone']);
        self::assertSame(self::CITY, $content['city']);
        self::assertSame(self::PLACE_ID, $content['placeId']);
    }
}
