<?php

declare(strict_types=1);

namespace App\Controller\Media;

use App\Repository\MediaRepository;
use App\Service\MediaCropperInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaCropController extends AbstractController
{
    private MediaCropperInterface $mediaCropper;
    private MediaRepository $mediaRepository;
    private LoggerInterface $logger;

    /**
     * MediaCropController constructor.
     *
     * @param MediaCropperInterface $mediaCropper
     * @param MediaRepository $mediaRepository
     * @param LoggerInterface $logger
     */
    public function __construct(MediaCropperInterface $mediaCropper, MediaRepository $mediaRepository, LoggerInterface $logger)
    {
        $this->mediaCropper = $mediaCropper;
        $this->mediaRepository = $mediaRepository;
        $this->logger = $logger;
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

        $imageCropParams = [
            0, 0, $media->getWidth()->get(), $media->getHeight()->get(),
        ];
        if ($request->request->has('imageCrop')) {
            $imageCropParams = explode(',', $request->get('imageCrop'));
        }

        try {
            $media = $this->mediaCropper->crop($media, $imageCropParams, $this->getUser());

            return new JsonResponse($media);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse($exception->getMessage());
        }
    }
}
