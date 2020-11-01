<?php

declare(strict_types=1);

namespace App\Controller\Media;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\User;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\Repository\UserEventRepositoryInterface;
use App\Service\MediaUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaUploadController extends AbstractController
{
    private MediaUploaderInterface $mediaUploader;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        MediaUploaderInterface $mediaUploader,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->mediaUploader = $mediaUploader;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
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
        /** @var UserInterface|User $user */
        $user = $this->getUser();
        $mediaCollection = $this->mediaUploader->uploadByRequest(
            $request,
            $this->getUser()
        );

        if ($user !== null) {
            $this->userEventRepository->log(
                new EventType(EventType::UPLOAD_PHOTO),
                $user,
                $this->requestUtmBuilder->build($request),
                EventContext::createByMedia(...$mediaCollection)
            );
        }

        return new JsonResponse($mediaCollection);
    }
}
