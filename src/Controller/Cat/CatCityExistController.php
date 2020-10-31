<?php

declare(strict_types=1);

namespace App\Controller\Cat;

use App\PetDomain\PetTypes;
use App\Repository\PetRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatCityExistController extends AbstractController
{
    /**
     * @Route("/api/v1/cat/city/exist", methods={"GET"}, name="public_cat_city_exist")
     *
     * @param PetRepositoryInterface $petRepository
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/cat/city/exist",
     *   tags={"Cat"},
     *   summary="cat cities list of pet profiles",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           {"city":"Moscow"}
     *     }
     *       }
     *    )
     * )
     */
    public function getCitiesList(PetRepositoryInterface $petRepository): JsonResponse
    {
        return new JsonResponse($petRepository->getExistCities(PetTypes::CAT), Response::HTTP_OK);
    }
}
