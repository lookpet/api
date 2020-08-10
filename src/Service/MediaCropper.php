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
use Psr\Log\LoggerInterface;
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

        $handle = fopen($media->getPublicUrl(), 'rb');
        $img = new \Imagick();
        $img->readImageFile($handle);
        $img->cropImage(128, 128, 0, 0);
        $img->writeImage($filePath);
        $stream = fopen($filePath, 'rb');
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
            new Mime($media->getMime()),
            new Width((string) $media->getWidth()),
            new Height((string) $media->getHeight())
        );

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        /*$imageToCrop = @imagecreatefromstring(
            $this->filesystem->read($media->getPath())
        );
        if ($imageToCrop !== false) {
            $coppedImage = imagecrop($imageToCrop, ['x' => $startXCoordinate, 'y' => $startYCoordinate, 'width' => $cropWidth, 'height' => $cropHeight]);
            if ($coppedImage !== false) {
                imagejpeg($coppedImage, $filePath);
                imagedestroy($coppedImage);
                imagedestroy($imageToCrop);


            }
        }*/

        return $media;
    }
}
