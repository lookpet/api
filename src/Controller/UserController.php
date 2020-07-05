<?php

declare(strict_types=1);

namespace App\Controller;

use App\CloudinaryBridge\Service\UploadService;
use App\Entity\Breeder;
use App\Entity\MediaUser;
use App\Entity\User;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use App\Service\MediaCloudinaryBuilder;
use App\Service\PetResponseBuilderInterface;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    private FilesystemInterface $filesystem;
    /**
     * @var PetResponseBuilderInterface
     */
    private PetResponseBuilderInterface $petResponseBuilder;

    public function __construct(FilesystemInterface $filesystem, PetResponseBuilderInterface $petResponseBuilder)
    {
        $this->filesystem = $filesystem;
        $this->petResponseBuilder = $petResponseBuilder;
    }

    /**
     * @Route("/api/v1/user", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        if ($request->request->has('firstName')) {
            $user->setFirstName($request->request->get('firstName'));
        }

        if ($request->request->has('phone')) {
            $user->setPhone($request->request->get('phone'));
        }

        if ($request->request->has('description')) {
            $user->setDescription($request->request->get('description'));
        }

        if ($request->request->has('city')) {
            $user->setCity($request->request->get('city'));
        }

        if ($request->request->has('breeder')) {
            $breeder = $user->hasBreeder() ? $user->getBreeder() : new Breeder($request->request->get('breeder'));
            $breeder->setName($request->request->get('breeder'));
            $user->setBreeder(
                $breeder
            );

            $entityManager->persist($breeder);
        }

        if ($request->request->has('slug')) {
            $user->setSlug($request->request->get('slug'));
        }

        $this->setPhotoIfExists($request, $user);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($user->jsonSerialize());
    }

    /**
     * @Route("/api/v1/user/photo", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addUserPhoto(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->setPhotoIfExists($request, $user);
    }

    /**
     * @Route("/api/v1/user/{slug}", methods={"GET"}, name="public_get_user")
     *
     * @param string $slug
     * @param UserRepository $userRepository
     *
     * @return JsonResponse
     */
    public function getUserBySlug(string $slug, UserRepository $userRepository): JsonResponse
    {
        if (!$slug) {
            return new JsonResponse([
                'message' => 'No slug was sent',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['slug' => $slug]);

        if ($user === null) {
            return new JsonResponse([
                'message' => 'No user was found',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $user
        );
    }

    /**
     * @Route("/api/v1/user/{slug}/pets", methods={"GET"}, name="user_pets")
     *
     * @param string $slug
     * @param PetRepository $petRepository
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getPets(string $slug, PetRepository $petRepository, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy([
            'slug' => $slug,
        ]);

        $pets = $petRepository->findBy([
            'user' => $user,
        ], [
            'updatedAt' => 'desc',
        ]);

        return $this->petResponseBuilder->build($user, ...$pets);
    }

    /**
     * @Route("/api/v1/users", methods={"GET"}, name="public_users")
     *
     * @param UserRepository $userRepository
     *
     * @return JsonResponse
     */
    public function search(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findBy([], [
            'updatedAt' => 'desc',
        ]);

        return new JsonResponse([
            'pets' => $users,
        ]);
    }

    private function setPhotoIfExists(Request $request, User $user): JsonResponse
    {
        if (!$request->files->has('photo')) {
            return new JsonResponse([
                'message' => 'Empty request',
            ], Response::HTTP_BAD_REQUEST);
        }

        $newPhoto = $request->files->get('photo');
        $entityManager = $this->getDoctrine()->getManager();

        if ($newPhoto) {
            $cloudinaryUpload = UploadService::upload(
                $newPhoto->getPathname()
            );
            $media = MediaCloudinaryBuilder::build(
                $cloudinaryUpload,
                $this->getUser()
            );

            $mediaUser = new MediaUser($media, $this->getUser());
            $entityManager->persist($media);
            $entityManager->persist($mediaUser);
            $entityManager->flush();

            return new JsonResponse($media);
        }

        return new JsonResponse([
            'message' => 'No file was uploaded',
        ], Response::HTTP_BAD_REQUEST);
    }

    private function uploadFile(File $file, string $destination = null): string
    {
        if ($destination === null) {
            $destination = '/pets/uploads/';
        }

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'rb');
        $this->filesystem->write(
                $destination . $newFilename,
                $stream
            );
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $destination . $newFilename;
    }
}
