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
use Gumlet\ImageResize;
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
        $fileName = Uuid::uuid4()->toString() . '.jpg';
        $filePath = '/tmp/' . $fileName;

        if (count($imageCropParams) === 4) {
            [
                $startXCoordinate,
                $startYCoordinate,
                $cropWidth,
                $cropHeight
            ] = $imageCropParams;
        }
        $file = $this->filesystem->read(
            $media->getPath()
        );

        $tmpFile = '/tmp/' . Uuid::uuid4()->toString() . '.jpg';
        file_put_contents($tmpFile, $file);

        $resizer = new ImageResize(
            $tmpFile
        );
        $resizer->freecrop($cropWidth, $cropHeight, $startXCoordinate, $startYCoordinate);

        $resizer->save(
            $filePath
        );

        $imageSize = getimagesize($filePath);

        $stream = fopen($tmpFile, 'rb');
        $this->filesystem->write(
            '/pets/uploads/' . $fileName,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        $media = new Media(
            $user,
            new FilePath('/pets/uploads/' . $fileName),
            new Url($_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $fileName),
            new Mime($imageSize['mime']),
            new Width((string) $imageSize[0]),
            new Height((string) $imageSize[1])
        );

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
