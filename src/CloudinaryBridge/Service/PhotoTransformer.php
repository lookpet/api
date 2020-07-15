<?php

namespace App\CloudinaryBridge\Service;

class PhotoTransformer implements PhotoTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function resizeCrop(string $publicId, int $width, int $height, int $startXCoordinate = 0, int $startYCoordinate = 0): string
    {
        Config::load();

        return cloudinary_url($publicId, [
            'x' => $startXCoordinate,
            'y' => $startYCoordinate,
            'width' => $width,
            'height' => $height,
            'crop' => 'crop',
        ]);
    }
}
