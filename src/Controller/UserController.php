<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Breeder;
use App\Entity\MediaUser;
use App\Entity\User;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use App\Service\MediaUploaderInterface;
use App\Service\PetResponseBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{

    /**
     * @var PetResponseBuilderInterface
     */
    private PetResponseBuilderInterface $petResponseBuilder;
    /**
     * @var MediaUploaderInterface
     */
    private MediaUploaderInterface $mediaUploader;

    public function __construct(MediaUploaderInterface $mediaUploader, PetResponseBuilderInterface $petResponseBuilder)
    {
        $this->petResponseBuilder = $petResponseBuilder;
        $this->mediaUploader = $mediaUploader;
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
     *
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

        return $this->petResponseBuilder->build($this->getUser(), ...$pets);
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

    private function setPhotoIfExists(Request $request): void
    {
        if (!$request->files->has('photo')) {
            return;
        }

        $mediaCollection = $this->mediaUploader->uploadByRequest(
            $this->getUser(),
            $request
        );
        $entityManager = $this->getDoctrine()->getManager();

        /**
         * Media $media.
         */
        foreach ($mediaCollection as $media) {
            $mediaUser = new MediaUser($media, $this->getUser());
            $entityManager->persist($media);
            $entityManager->persist($mediaUser);
            $entityManager->flush();
        }
    }
}
