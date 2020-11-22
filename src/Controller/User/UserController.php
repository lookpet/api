<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use App\Service\PetResponseBuilderInterface;
use App\Service\UserResponseBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    private PetResponseBuilderInterface $petResponseBuilder;
    private UserResponseBuilderInterface $userResponseBuilder;

    public function __construct(
        UserResponseBuilderInterface $userResponseBuilder,
        PetResponseBuilderInterface $petResponseBuilder
    ) {
        $this->petResponseBuilder = $petResponseBuilder;
        $this->userResponseBuilder = $userResponseBuilder;
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

        return $this->userResponseBuilder->buildForOneUser(
            $user,
            $this->getUser()
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
}
