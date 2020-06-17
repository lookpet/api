<?php

declare(strict_types=1);

namespace App\Controller;

use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatBreedExistController extends AbstractController
{
    /**
     * @Route("/api/v1/cat/breeds/exist", methods={"GET"}, name="public_cat_breeds_exist")
     *
     * @param PetRepositoryInterface $petRepository
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/cat/breeds/exist",
     *   tags={"Cat"},
     *   summary="cat breed list that have profiles",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           {"breed":"Main Coon"}
     *     }
     *       }
     *    )
     * )
     */
    public function getBreedList(PetRepositoryInterface $petRepository): JsonResponse
    {
        return new JsonResponse($petRepository->getExistBreeds(PetTypes::CAT), Response::HTTP_OK);
    }
}
