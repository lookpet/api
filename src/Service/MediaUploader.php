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

        if (count($newPhotos) === 0) {
            return [];
        }

        $mediaCollection = [];

        $imageCropParams = [];
        if ($request->request->has('imageCrop')) {
           $imageCropParams = $request->get('imageCrop');
        }

        foreach ($newPhotos as $key => $newPhoto) {
            $imageSize = getimagesize($newPhoto->getPathname());
            $startXCoordinate = 0;
            $startYCoordinate = 0;
            $cropWidth = $imageSize[0];
            $cropHeight = $imageSize[1];
            if (isset($imageCropParams[$key])) {
                [
                    $startXCoordinate,
                    $startYCoordinate,
                    $cropWidth,
                    $cropHeight
                ] = implode(',', $imageCropParams[$key]);
            }

            $resizer = new ImageResize(
                $newPhoto->getPathname()
            );
            $resizer->freecrop($cropWidth, $cropHeight, $startXCoordinate, $startYCoordinate);
            $resizer->resizeToBestFit(1080,1080);
            $fileName = sha1(microtime()).'.jpg';
            $filePath = '/tmp/'.$fileName;
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

            unlink($filePath);
            unlink($newPhoto->getPathname());

            $media = new Media(
                $user,
                new FilePath('/pets/uploads/' . $fileName),
                new Url($_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $fileName),
                new Mime($imageSize['mime']),
                new Width((string) $imageSize[0]),
                new Height((string) $imageSize[1])
            );

            $mediaCollection[] = $media;
            $this->entityManager->persist($media);
            $this->entityManager->flush();
        }

        return $mediaCollection;
    }

    /**
     * @param File $file
     * @param string|null $destination
     *
     * @return string
     * Грузим фото в cloudinary
     * Забираем кроп-ресайз
     * Заливаем на s3
     * Удаляем с cloudinary
     */
    private function uploadFilde(File $file, string $destination = null): string
    {
        if ($destination === null) {
            $destination = '/pets/uploads/';
        }

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'rb');
        $this->filesystem->write(
            $destination . $newFilename,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $destination . $newFilename;
    }
}
