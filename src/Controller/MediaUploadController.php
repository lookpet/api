<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Service\MediaUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaUploadController extends AbstractController
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
     * @Route("/api/v1/media", methods={"POST"}, name="public_post_media")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $mediaCollection = $this->mediaUploader->uploadByRequest(
            $request,
            $this->getUser()
        );

        return new JsonResponse($mediaCollection);
    }
}
