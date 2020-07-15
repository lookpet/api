<?php

namespace App\CloudinaryBridge\Service;

class Config
{
    public static function load(): void
    {
        \Cloudinary::config_from_url($_ENV['CLOUDINARY_URL']);
    }
}
