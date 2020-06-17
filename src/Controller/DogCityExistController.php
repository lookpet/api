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

final class DogCityExistController extends AbstractController
{
    /**
     * @Route("/api/v1/dog/city/exist", methods={"GET"}, name="public_dog_city_exist")
     *
     * @param PetRepositoryInterface $petRepository
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/dog/city/exist",
     *   tags={"Dog"},
     *   summary="Dog cities list of pet profiles",
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
        return new JsonResponse($petRepository->getExistCities(PetTypes::DOG), Response::HTTP_OK);
    }
}
