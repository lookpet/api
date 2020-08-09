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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class SimpleMediaUploader implements MediaUploaderInterface
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
    public function uploadByRequest(Request $request, ?UserInterface $user = null): iterable
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

        foreach ($newPhotos as $key => $newPhoto) {
            $fileName = Uuid::uuid4()->toString() . '.jpg';
            $stream = fopen($newPhoto->getPathname(), 'rb');
            $this->filesystem->write(
                '/pets/uploads/' . $fileName,
                $stream
            );
            if (is_resource($stream)) {
                fclose($stream);
            }
            $imageUrl = $_ENV['AWS_S3_PATH'] . '/pets/uploads/' . $fileName;
            $imageInfo = getimagesize($imageUrl);

            $media = new Media(
                $user,
                new FilePath('/pets/uploads/' . $fileName),
                new Url($imageUrl),
                new Mime($imageInfo['mime']),
                new Width((string) $imageInfo[0]),
                new Height((string) $imageInfo[1])
            );

            $mediaCollection[] = $media;
            $this->entityManager->persist($media);
            $this->entityManager->flush();
        }

        return $mediaCollection;
    }
}
