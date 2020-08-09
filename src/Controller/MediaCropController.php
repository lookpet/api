<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Service\MediaUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaCropController extends AbstractController
{
    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;
    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $filesystem;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var MediaUploaderInterface
     */
    private MediaUploaderInterface $mediaUploader;

    public function __construct(
        MediaUploaderInterface $mediaUploader,
        MediaRepository $mediaRepository,
        FilesystemInterface $filesystem,
        EntityManagerInterface $entityManager
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->mediaUploader = $mediaUploader;
    }

    /**
     * @Route("/api/v1/media/{id}/crop", methods={"POST"}, name="public_post_media_crop")
     *
     * @param string $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(string $id, Request $request): JsonResponse
    {
        $media = $this->mediaRepository->find($id);

        if ($media === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($media);
    }
}
