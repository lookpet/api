<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Component\Security\Core\User\UserInterface;

interface MediaCropperInterface
{
    public function crop(Media $media, array $imageCropParams = [], ?UserInterface $user = null): Media;
}
