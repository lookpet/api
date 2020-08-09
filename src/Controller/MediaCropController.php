<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Service\MediaCropperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaCropController extends AbstractController
{
    /**
     * @var MediaCropperInterface
     */
    private MediaCropperInterface $mediaCropper;
    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;

    public function __construct(MediaCropperInterface $mediaCropper, MediaRepository $mediaRepository)
    {
        $this->mediaCropper = $mediaCropper;
        $this->mediaRepository = $mediaRepository;
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

        if (!$media->hasAccess($this->getUser())) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        if ($media === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $imageCropParams = [
            0, 0, $media->getWidth()->get(), $media->getHeight()->get(),
        ];
        if ($request->request->has('imageCrop')) {
            $imageCropParams = explode(',', $request->get('imageCrop'));
        }

        $media = $this->mediaCropper->crop($media, $imageCropParams, $this->getUser());

        return new JsonResponse($media);
    }
}
