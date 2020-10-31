<?php

namespace Tests\Functional\V1\Media;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group functional
 */
final class MediaUploadTest extends WebTestCase
{
    private const UPLOAD_MEDIA = '/api/v1/media';
    private const PHOTO_URL = __DIR__ . '/../../../DataFixtures/Media/photo.jpg';

    public function testItUploadPhoto(): void
    {
        $client = static::createClient();

        $imageSize = getimagesize(self::PHOTO_URL);
        $client->request(
            Request::METHOD_POST,
            self::UPLOAD_MEDIA,
            [],
            [
                'photo' => new UploadedFile(
                    self::PHOTO_URL,
                    'test'
                ),
            ],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $media = array_shift($content);
        self::assertSame((string) $imageSize[0], $media['width']);
        self::assertSame((string) $imageSize[1], $media['height']);
        self::assertEqualsWithDelta(
            (new \DateTimeImmutable('now'))->getTimestamp(),
            (new \DateTimeImmutable($media['created_at']['date']))->getTimestamp(),
            3
        );
    }
}
