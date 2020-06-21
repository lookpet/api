<?php


namespace App\Service;


use App\Entity\Media;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaCloudinaryBuilder
{
    public static function build(array $cloudinaryUpload, UserInterface $user): Media
    {
        $media = new Media();
        $media->setPublicUrl($cloudinaryUpload['secure_url']);
        $media->setCloudinaryResponse(json_encode($cloudinaryUpload));
        $media->setUser($user);
        $media->setIsPublished(true);
        $media->setHeight($cloudinaryUpload['height']);
        $media->setWidth($cloudinaryUpload['width']);
        $media->setCloudinaryPublicId($cloudinaryUpload['public_id']);
        $media->setSize('original');
        return $media;
    }
}