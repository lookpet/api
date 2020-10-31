<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Dto\User\UserDtoBuilderInterface;
use App\Entity\MediaUser;
use App\Entity\User;
use App\Service\MediaUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserUpdateController extends AbstractController
{
    private MediaUploaderInterface $mediaUploader;
    private UserDtoBuilderInterface $userDtoBuilder;
    private EntityManagerInterface $entityManager;

    public function __construct(
        MediaUploaderInterface $mediaUploader,
        UserDtoBuilderInterface $userDtoBuilder,
        EntityManagerInterface $entityManager)
    {
        $this->mediaUploader = $mediaUploader;
        $this->userDtoBuilder = $userDtoBuilder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/user", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->setPhotoIfExists($request);
        $userDto = $this->userDtoBuilder->build($request);
        $user->updateFromDto($userDto);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse($user, Response::HTTP_OK);
    }

    private function setPhotoIfExists(Request $request): void
    {
        if (!$request->files->has('photo')) {
            return;
        }

        /** @var User $user */
        $user = $this->getUser();

        $mediaCollection = $this->mediaUploader->uploadByRequest(
            $request,
            $this->getUser()
        );

        /**
         * Media $media.
         */
        foreach ($mediaCollection as $media) {
            $mediaUser = new MediaUser($media, $user);
            $user->addMedia($mediaUser);
            $this->entityManager->persist($user);
            $this->entityManager->persist($mediaUser);
            $this->entityManager->flush();
        }
    }
}
