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
use Gedmo\Sluggable\Util\Urlizer;
use Gumlet\ImageResize;
use League\Flysystem\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaUploader implements MediaUploaderInterface
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

    /**
     * {@inheritdoc}
     */
    public function uploadByRequest(UserInterface $user, Request $request): iterable
    {
        if (!$request->files->has('photo')) {
            return [];
        }

        $newPhotos = $request->files->get('photo');

        if ($newPhotos instanceof UploadedFile) {
            $newPhotos = [$newPhotos];
        }

        if (count($newPhotos) === 0) {
            return [];
        }

        $mediaCollection = [];

        $imageCropParams = [];
        if ($request->request->has('imageCrop')) {
            $imageCropParams = $request->get('imageCrop');
        }

        /**
         * Fil.
         */
        foreach ($newPhotos as $key => $newPhoto) {
            $this->correctOrientation($newPhoto);
            $imageSize = getimagesize($newPhoto->getPathname());
            $startXCoordinate = 0;
            $startYCoordinate = 0;
            $cropWidth = $imageSize[0];
            $cropHeight = $imageSize[1];
            if (isset($imageCropParams[$key])) {
                $cropInformation = explode(',', $imageCropParams[$key]);
                if (count($cropInformation) === 4) {
                    [
                        $startXCoordinate,
                        $startYCoordinate,
                        $cropWidth,
                        $cropHeight
                    ] = $cropInformation;
                }
            }

            $resizer = new ImageResize(
                $newPhoto->getPathname()
            );
            $resizer->freecrop($cropWidth, $cropHeight, $startXCoordinate, $startYCoordinate);
            $fileName = Uuid::uuid4()->toString() . '.jpg';
            $filePath = '/tmp/' . $fileName;
            $resizer->save(
                $filePath
            );

            $imageSize = getimagesize($filePath);

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
                new Mime($imageSize['mime']),
                new Width((string) $imageSize[0]),
                new Height((string) $imageSize[1])
            );

            unlink($filePath);
            unlink($newPhoto->getPathname());

            $mediaCollection[] = $media;
            $this->entityManager->persist($media);
            $this->entityManager->flush();
        }

        return $mediaCollection;
    }

    private function correctOrientation($filename):void
    {
        $image = imagecreatefromjpeg($filename);
        $exif = exif_read_data($filename);
        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image,90,0);
                    break;
                case 3:
                    $image = imagerotate($image,180,0);
                    break;
                case 6:
                    $image = imagerotate($image,-90,0);
                    break;
            }
            imagejpeg($image, $filename, 100);
        }
    }
}
