<?php

namespace App\CloudinaryBridge\Service;

use Cloudinary\Uploader;

class MediaUploader implements MediaUploaderInterface
{
    public function upload(string $filePath, array $options = [])
    {
        \Cloudinary::config_from_url($_ENV['CLOUDINARY_URL']);

        return Uploader::upload($filePath, $options);
    }
}
