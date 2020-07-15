<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaDeleteController extends AbstractController
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

    public function __construct(
        MediaRepository $mediaRepository,
        FilesystemInterface $filesystem,
        EntityManagerInterface $entityManager
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/media/{id}", methods={"DELETE"}, name="delete_media")
     *
     * @param string $id
     *
     * @return JsonResponse
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function delete(string $id): JsonResponse
    {
        $media = $this->mediaRepository->find($id);
        if ($media->getUser()->getId() !== $this->getUser()->getId()) {
            return new JsonResponse([
                'message' => 'Wrong user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->filesystem->delete($media->getPath());
        $this->entityManager->remove($media);
        $this->entityManager->flush();

        return new JsonResponse();
    }
}
