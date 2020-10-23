<?php

namespace App\CloudinaryBridge\Service;

use Cloudinary\Uploader;

class CloudinaryClient implements CloudinaryClientInterface
{
    public function upload(string $filePath, array $options = [])
    {
        \Cloudinary::config_from_url(getenv('CLOUDINARY_URL'));

        return Uploader::upload($filePath, $options);
    }

    public function delete(string $publicId): void
    {
        \Cloudinary\Uploader::destroy($publicId);
    }
}
