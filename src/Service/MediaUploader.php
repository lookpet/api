<?php

namespace App\Service;

use App\CloudinaryBridge\Service\CloudinaryClientInterface as CloudinaryMediaUploader;
use App\CloudinaryBridge\Service\PhotoTransformerInterface;
use App\Entity\Media;
use App\PetDomain\VO\FilePath;
use App\PetDomain\VO\Height;
use App\PetDomain\VO\Mime;
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
            if ($request->request->has('x')) {
                $startXCoordinate = $request->request->get('x');
            }
            $startYCoordinate = 0;
            if ($request->request->has('x')) {
                $startYCoordinate = $request->request->get('y');
            }
            $cropWidth = 1080;
            if ($request->request->has('width')) {
                $cropWidth = $request->request->get('width');
            }
            $cropHeight = 1080;
            if ($request->request->has('height')) {
                $cropHeight = $request->request->get('height');
            }

            $cloudinaryUpload = $this->cloudinaryUploader->upload(
                $newPhoto->getPathname(),
                [
                    'quality' => 80,
                    'format' => 'jpg',
                ]
            );
            $publicId = $cloudinaryUpload['public_id'];

            $cloudinaryTransformUrl = $this->photoTransformer->resizeCrop(
                $publicId,
                $cropWidth,
                $cropHeight,
                $startXCoordinate,
                $startYCoordinate
            );

            $stream = fopen($cloudinaryTransformUrl, 'rb');
            $this->filesystem->write(
                '/pets/uploads/' . $publicId,
                $stream
            );
            if (is_resource($stream)) {
                fclose($stream);
            }

            $imageSize = getimagesize($cloudinaryTransformUrl);

            $media = new Media(
                $user,
                new FilePath('/pets/uploads/' . $cloudinaryUpload['public_id']),
                new Url($_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $cloudinaryUpload['public_id']),
                new Mime($imageSize['mime']),
                new Width($imageSize[0]),
                new Height($imageSize[1])
            );

            $mediaCollection[] = $media;
            $this->entityManager->persist($media);
            $this->entityManager->flush();
            $this->cloudinaryUploader->delete($publicId);
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
