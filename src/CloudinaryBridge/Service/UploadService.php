<?php

namespace App\CloudinaryBridge\Service;

use Cloudinary\Uploader;

class UploadService
{
    public static function upload(string $filePath)
    {
        \Cloudinary::config_from_url($_ENV['CLOUDINARY_URL']);

        return Uploader::upload($filePath);
    }
}
