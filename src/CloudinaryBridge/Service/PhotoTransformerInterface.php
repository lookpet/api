<?php

namespace App\CloudinaryBridge\Service;

use App\PetDomain\VO\Height;
use App\PetDomain\VO\Width;

interface PhotoTransformerInterface
{
    public function resizeCrop(string $publicId, int $width, int $height, int $startXCoordinate = 0, int $startYCoordinate = 0);

    public function crop(string $publicId, Width $width, Height $height);
}
