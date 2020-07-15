<?php

namespace App\Service;

use App\CloudinaryBridge\Service\MediaUploaderInterface as CloudinaryMediaUploader;
use App\CloudinaryBridge\Service\PhotoTransformerInterface;
use App\Entity\Media;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Url;
use App\PetDomain\VO\Width;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
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
    /**
     * @var CloudinaryMediaUploader
     */
    private CloudinaryMediaUploader $cloudinaryUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $filesystem,
        PhotoTransformerInterface $photoTransformer,
        CloudinaryMediaUploader $cloudinaryUploader
    ) {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->photoTransformer = $photoTransformer;
        $this->cloudinaryUploader = $cloudinaryUploader;
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

        foreach ($newPhotos as $newPhoto) {
            $startXCoordinate = 0;
            if ($request->request->has('startXCoordinate')) {
                $startXCoordinate = $request->request->get('startXCoordinate');
            }
            $startYCoordinate = 0;
            if ($request->request->has('startYCoordinate')) {
                $startYCoordinate = $request->request->get('startYCoordinate');
            }
            $cropWidth = 1080;
            if ($request->request->has('cropWidth')) {
                $cropWidth = $request->request->get('cropWidth');
            }
            $cropHeight = 1080;
            if ($request->request->has('cropHeight')) {
                $cropHeight = $request->request->get('cropHeight');
            }

            $cloudinaryUpload = $this->cloudinaryUploader->upload(
                $newPhoto->getPathname(),
                [
                    'quality' => 80,
                    'format' => 'jpg',
                ]
            );

            $cloudinaryTransformUrl = $this->photoTransformer->resizeCrop(
                $cloudinaryUpload['public_id'],
                $cropWidth,
                $cropHeight,
                $startXCoordinate,
                $startYCoordinate
            );

            $stream = fopen($cloudinaryTransformUrl, 'rb');
            $this->filesystem->write(
                '/pets/uploads/' . $cloudinaryUpload['public_id'],
                $stream
            );
            if (is_resource($stream)) {
                fclose($stream);
            }

            $imageSize = getimagesize($cloudinaryTransformUrl);

            $media = new Media(
                $user,
                new Url($_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $cloudinaryUpload['public_id']),
                new Width($imageSize[0]),
                new Height($imageSize[1])
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
