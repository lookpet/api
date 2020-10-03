<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\Entity\PetLike;
use App\Repository\PetLikeRepositoryInterface;
use App\Repository\PetRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetLikeController extends AbstractController
{
    private PetRepositoryInterface $petRepository;
    private PetLikeRepositoryInterface $petLikeRepository;

    public function __construct(PetRepositoryInterface $petRepository, PetLikeRepositoryInterface $petLikeRepository)
    {
        $this->petRepository = $petRepository;
        $this->petLikeRepository = $petLikeRepository;
    }

    /**
     * @Route("/api/v1/pet/{slug}/like", methods={"POST"}, name="pet_like")
     *
     * @param string $slug
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/pet/{slug}/like",
     *   tags={"Like"},
     *   summary="Lets like pets",
     *   description="",
     *   operationId="petLike",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "hasLike": true,
     *           "total": 30,
     *          }
     *       }
     *    )
     * )
     */
    public function like(string $slug): JsonResponse
    {
        $pets = $this->petRepository->findBy([
            'slug' => $slug,
        ]);

        if (count($pets) === 0) {
            return new JsonResponse([
                'message' => 'Pet not exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $pet = array_pop($pets);

        $petLikes = $this->petLikeRepository->getPetLikes($this->getUser(), $pet);

        if (count($petLikes) === 0) {
            $petLike = new PetLike($pet, $this->getUser());
            $pet->addLike(
                $petLike
            );
            $this->getDoctrine()->getManager()->persist($petLike);
        } else {
            $petLike = $petLikes[0];
            $pet->removeLike(...[$petLike]);
            $this->getDoctrine()->getManager()->remove($petLike);
        }

        $this->getDoctrine()->getManager()->persist($pet);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            [
                'hasLike' => $pet->hasLike($this->getUser()),
                'total' => count($pet->getLikes()),
            ]
        );
    }
}
