<?php

namespace App\CloudinaryBridge\Service;

use App\PetDomain\VO\Height;
use App\PetDomain\VO\Width;

class PhotoTransformer implements PhotoTransformerInterface
{
    public function __construct()
    {
        Config::load();
    }

    /**
     * {@inheritdoc}
     */
    public function resizeCrop(string $publicId, int $width, int $height, int $startXCoordinate = 0, int $startYCoordinate = 0): string
    {
        return cloudinary_url($publicId, [
            'x' => $startXCoordinate,
            'y' => $startYCoordinate,
            'width' => $width,
            'height' => $height,
            'crop' => 'crop',
        ]);
    }

    public function crop(string $publicId, Width $width, Height $height)
    {
        return cloudinary_url($publicId, [
            'width' => $width->get(),
            'height' => $height->get(),
            'crop' => 'fill',
            'aspect_ratio' => '4:3',
            'effect' => 'sharpen',
        ]);
    }
}
