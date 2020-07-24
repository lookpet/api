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
use Intervention\Image\Image;
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
     * @var Image
     */
    private Image $image;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $filesystem
    ) {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
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
            $fileName = Uuid::uuid4()->toString() . '.jpg';
            $filePath = '/tmp/' . $fileName;

//            \Intervention\Image\Image::make();
//            $image = $this->image->make($newPhoto->getPathname());
//            $image->orientate();
//            $image->crop($cropWidth, $cropHeight, $startXCoordinate, $startYCoordinate);
//            $image->save($filePath);

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

    function correctImageOrientation($filename) {
        $exif = \exif_read_data($filename);
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }

    private function correctOrientation($filename):void
    {
        $i = get_loaded_extensions();
        if(extension_loaded("exif")){
            return;
        }
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
