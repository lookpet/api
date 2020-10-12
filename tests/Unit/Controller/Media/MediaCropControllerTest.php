<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\Media;

use App\Controller\Media\MediaCropController;
use App\Entity\Media;
use App\Entity\User;
use App\PetDomain\VO\FilePath;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Mime;
use App\PetDomain\VO\Url;
use App\PetDomain\VO\Width;
use App\Repository\MediaRepositoryInterface;
use App\Service\MediaCropperInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * group unit.
 */
class MediaCropControllerTest extends TestCase
{
    private const ID = 'some-id';
    private const FILE_PATH = '/file-path';
    private const URL = 'https://file-path';
    private const MIME_TYPE = 'image/jpeg';
    private const WIDTH = '555';
    private const HEIGHT = '444';

    private const X = 100;
    private const Y = 200;
    private MediaCropperInterface $mediaCropper;
    private MediaRepositoryInterface $mediaRepository;
    private LoggerInterface $logger;
    private MediaCropController $mediaCropController;
    private User $user;

    public function testItCropsMedia(): void
    {
        $request = new Request([], [
            'imageCrop' => sprintf('%d,%d,%d,%d', self::X, self::Y, self::WIDTH, self::HEIGHT),
        ]);
        $container = $this->createContainer('security.token_storage',
            $this->createMock(TokenStorageInterface::class)
        );

        $this->mediaCropController->setContainer(
            $container
        );

        /** @var ContainerInterface|MockObject $container */
        $container
            ->expects(self::atLeastOnce())
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);

        $media = new Media(
            $this->user,
            new FilePath(self::FILE_PATH),
            new Url(self::URL),
            new Mime(self::MIME_TYPE),
            new Width(self::WIDTH),
            new Height(self::HEIGHT),
            self::ID
        );

        $this->mediaRepository
            ->expects(self::atLeastOnce())
            ->method('findById')
            ->with(self::ID)
            ->willReturn($media);

        $this
            ->mediaCropper
            ->expects(self::once())
            ->method('crop')
            ->with($media, [
                self::X, self::Y, floatval(self::WIDTH), floatval(self::HEIGHT),
            ], null)
            ->willReturn($media);

        $result = $this->mediaCropController->crop(
            self::ID,
            $request
        );

        $decodedResponse = json_decode($result->getContent());
        self::assertSame(Response::HTTP_OK, $result->getStatusCode());
        self::assertSame(self::ID, $decodedResponse->id);
        self::assertSame(self::WIDTH, $decodedResponse->width);
        self::assertSame(self::HEIGHT, $decodedResponse->height);
        self::assertSame(self::URL, $decodedResponse->publicUrl);
    }

    public function testItReturnsNotFoundBecauseMediaIsNotExist(): void
    {
        $request = new Request();
        $this->mediaRepository
            ->expects(self::once())
            ->method('findById')
            ->with(self::ID)
            ->willReturn(null);

        $result = $this->mediaCropController->crop(
            self::ID,
            $request
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $result->getStatusCode());
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->mediaCropper = $this->createMock(MediaCropperInterface::class);
        $this->mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->user = $this->createMock(User::class);

        $this->mediaCropController = new MediaCropController(
            $this->mediaCropper,
            $this->mediaRepository,
            $this->logger
        );
    }

    private function createContainer($serviceId, $serviceObject): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with($serviceId)
            ->willReturn($serviceObject);

        return $container;
    }
}
