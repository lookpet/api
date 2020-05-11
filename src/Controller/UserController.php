<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

final class UserController extends AbstractController
{
    /**
     * @Route("/api/v1/user", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->request->has('firstName')) {
            $user->setFirstName($request->request->get('firstName'));
        }

        if ($request->request->has('phone')) {
            $user->setPhone($request->request->get('phone'));
        }

        if ($request->request->has('description')) {
            $user->setDescription($request->request->get('description'));
        }

        if ($request->request->has('slug')) {
            $user->setSlug($request->request->get('slug'));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            $user
        ]);
    }

    /**
     * @Route("/api/v1/user/photo", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addUserPhoto(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->setPhotoIfExists($request, $user);
    }

    /**
     * @Route("/api/v1/user/{slug}", methods={"GET"})
     *
     * @param string $slug
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getUserBySlug(string $slug, UserRepository $userRepository):JsonResponse
    {
        if (!$slug) {
            return new JsonResponse([
                'message' => 'No slug was sent'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['slug' => $slug]);

        if ($user === null) {
            return new JsonResponse([
                'message' => 'No user was found'
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $user
        );
    }


    private function setPhotoIfExists(Request $request, User $user): JsonResponse
    {
        if (!$request->files->has('photo')) {
            return new JsonResponse([
                'message' => 'Empty request'
            ], Response::HTTP_BAD_REQUEST);
        }

        $newPhoto = $request->files->get('photo');
        $entityManager = $this->getDoctrine()->getManager();
        if ($newPhoto) {
            $newFile = $this->uploadFile($newPhoto);
            $media = new Media();
            $media->setPublicUrl('/uploads/' . $newFile);
            $media->setUser($user);
            $media->setSize('original');
            $entityManager->persist($media);
            $entityManager->flush();

            return new JsonResponse($media);
        }

        return new JsonResponse([
            'message' => 'No file was uploaded'
        ], Response::HTTP_BAD_REQUEST);
    }

    private function uploadFile(File $file, string $destination = null): string
    {
        if ($destination === null) {
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
        }

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $file->move($destination, $newFilename);

        return $newFilename;
    }
}
