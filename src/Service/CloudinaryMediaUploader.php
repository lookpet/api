<?php

namespace App\Service;

use App\CloudinaryBridge\Service\CloudinaryClientInterface as CloudinaryClient;
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

class CloudinaryMediaUploader
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
     * @var CloudinaryClient
     */
    private CloudinaryClient $cloudinaryUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $filesystem,
        PhotoTransformerInterface $photoTransformer,
        CloudinaryClient $cloudinaryUploader
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
            $imageSize = getimagesize($newPhoto->getPathname());
            $startXCoordinate = 0;
            if ($request->request->has('x')) {
                $startXCoordinate = $request->request->get('x');
            }
            $startYCoordinate = 0;
            if ($request->request->has('y')) {
                $startYCoordinate = $request->request->get('y');
            }
            $cropWidth = $imageSize[0];
            if ($request->request->has('width')) {
                $cropWidth = $request->request->get('width');
            }
            $cropHeight = $imageSize[1];
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
            sleep(5);



            if ($request->request->has('width') && $request->request->has('height')) {
                $cloudinaryTransformUrl = $this->photoTransformer->resizeCrop(
                    $publicId,
                    $cropWidth,
                    $cropHeight,
                    $startXCoordinate,
                    $startYCoordinate
                );
            } else {
                $cloudinaryTransformUrl =  $this->photoTransformer->crop(
                    $publicId,
                    new Width((string) 1080),
                    new Height((string) 1080)
                );
            }

            $imageSize = getimagesize($cloudinaryTransformUrl);
//            new Url($_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $cloudinaryUpload['public_id']),

            $media = new Media(
                $user,
                new FilePath('/pets/uploads/' . $cloudinaryUpload['public_id']),
                new Url($cloudinaryTransformUrl),
                new Mime($imageSize['mime']),
                new Width((string) $imageSize[0]),
                new Height((string) $imageSize[1]),
                $publicId,
                new Url($cloudinaryTransformUrl)
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
