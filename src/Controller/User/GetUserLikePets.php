<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Repository\UserRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetUserLikePets extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/v1/user/{slug}/like", methods={"GET"}, name="get_user_like")
     *
     * @param string $slug
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/user/{slug}/like",
     *   tags={"Like"},
     *   summary="Lets get list of user liked pets",
     *   description="",
     *   operationId="userPetLike",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *
     *          }
     *       }
     *    )
     * )
     */
    public function likes(string $slug): JsonResponse
    {
        $user = $this->userRepository->findBySlug($slug);
        $petLikes = $user->getPetLikes();

        return new JsonResponse($petLikes->toArray());
    }
}
