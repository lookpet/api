<?php

declare(strict_types=1);

namespace App\Controller\Dog;

use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DogBreedExistController extends AbstractController
{
    /**
     * @Route("/api/v1/dog/breed/exist", methods={"GET"}, name="public_dog_breeds_exist")
     *
     * @param PetRepositoryInterface $petRepository
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/dog/breed/exist",
     *   tags={"Dog"},
     *   summary="Dog breed list that have profiles",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           {"breed":"Siberian Husky"}
     *     }
     *       }
     *    )
     * )
     */
    public function getBreedList(PetRepositoryInterface $petRepository): JsonResponse
    {
        return new JsonResponse($petRepository->getExistBreeds(PetTypes::DOG), Response::HTTP_OK);
    }
}
