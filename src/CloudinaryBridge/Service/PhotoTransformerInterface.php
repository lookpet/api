<?php

namespace App\CloudinaryBridge\Service;

interface PhotoTransformerInterface
{
    public function resizeCrop(string $publicId, int $width, int $height, int $startXCoordinate = 0, int $startYCoordinate = 0);
}
