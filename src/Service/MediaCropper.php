<?php

namespace App\Service;

use App\CloudinaryBridge\Service\PhotoTransformerInterface;
use App\Entity\Media;
use App\PetDomain\VO\FilePath;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Mime;
use App\PetDomain\VO\Url;
use App\PetDomain\VO\Width;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaCropper implements MediaCropperInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $filesystem;
    /**
     * @var PhotoTransformerInterface
     */
    private PhotoTransformerInterface $photoTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $filesystem,
        PhotoTransformerInterface $photoTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->photoTransformer = $photoTransformer;
    }

    public function crop(Media $media, array $imageCropParams = [], ?UserInterface $user = null): Media
    {
        $mediaId = Uuid::uuid4()->toString();
        $fileName = $mediaId . '.jpg';

        if (count($imageCropParams) === 4) {
            [
                $startXCoordinate,
                $startYCoordinate,
                $cropWidth,
                $cropHeight
            ] = $imageCropParams;
        }

        $filePath = sprintf(
            'http://photo-proxy-production.eu-central-1.elasticbeanstalk.com/cx%d,cy%d,cw%d,ch%d,%dx/%s',
            $startXCoordinate,
            $startYCoordinate,
            $cropWidth,
            $cropHeight,
            1080,
            $media->getPublicUrl()
        );

        $stream = fopen($filePath, 'rb');
        $streamContent = stream_get_contents($stream);
        $imageInfo = getimagesizefromstring($streamContent);
        $this->filesystem->write(
            '/pets/uploads/' . $fileName,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        $relativeFilePath = '/pets/uploads/' . $fileName;
        $imageUrl = $_ENV['AWS_S3_PATH'] . $relativeFilePath;
//        $imageInfo = getimagesize($imageUrl);

        $media = new Media(
            $user,
            new FilePath($relativeFilePath),
            new Url($imageUrl),
            new Mime($imageInfo['mime']),
            new Width((string) $imageInfo[0]),
            new Height((string) $imageInfo[1]),
            $mediaId
        );

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
