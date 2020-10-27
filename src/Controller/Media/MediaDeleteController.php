<?php

declare(strict_types=1);

namespace App\Controller\Media;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\PetDomain\VO\EventType;
use App\Repository\MediaRepository;
use App\Repository\UserEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaDeleteController extends AbstractController
{
    private MediaRepository $mediaRepository;
    private FilesystemInterface $filesystem;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        MediaRepository $mediaRepository,
        FilesystemInterface $filesystem,
        EntityManagerInterface $entityManager,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/media/{id}", methods={"DELETE"}, name="delete_media")
     *
     * @param string $id
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function delete(string $id, Request $request): JsonResponse
    {
        $media = $this->mediaRepository->find($id);

        if ($media === null) {
            return new JsonResponse([
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$media->hasAccess($this->getUser())) {
            return new JsonResponse([
                'message' => 'Wrong user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->filesystem->delete($media->getPath());
        $this->entityManager->remove($media);
        $this->entityManager->flush();

        $this->userEventRepository->log(
            new EventType(EventType::DELETE_PHOTO),
            $this->getUser(),
            $this->requestUtmBuilder->build($request)
        );

        return new JsonResponse();
    }
}
