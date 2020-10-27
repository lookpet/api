<?php

declare(strict_types=1);

namespace App\Controller\Media;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\PetDomain\VO\EventType;
use App\Repository\MediaRepository;
use App\Repository\MediaRepositoryInterface;
use App\Repository\UserEventRepositoryInterface;
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
    private MediaRepositoryInterface $mediaRepository;
    private LoggerInterface $logger;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    /**
     * MediaCropController constructor.
     *
     * @param MediaCropperInterface $mediaCropper
     * @param MediaRepositoryInterface $mediaRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        MediaCropperInterface $mediaCropper,
        MediaRepositoryInterface $mediaRepository,
        LoggerInterface $logger,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->mediaCropper = $mediaCropper;
        $this->mediaRepository = $mediaRepository;
        $this->logger = $logger;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/media/{id}/crop", methods={"POST"}, name="public_post_media_crop")
     *
     * @param string $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function crop(string $id, Request $request): JsonResponse
    {
        $media = $this->mediaRepository->findById($id);

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

            $this->userEventRepository->log(
                new EventType(EventType::CROP_PHOTO),
                $this->getUser(),
                $this->requestUtmBuilder->build($request)
            );

            return new JsonResponse($media);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
