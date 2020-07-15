<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MediaDeleteController extends AbstractController
{
    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function delete(string $id): JsonResponse
    {
        $media = $this->mediaRepository->find($id);
        $this->mediaRepository->find($media);
//       $media-
       //find in pets
        //if find in pets remove
        //if find in users remove
    }
}
