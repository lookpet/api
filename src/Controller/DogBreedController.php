<?php

declare(strict_types=1);

namespace App\Controller;

use App\PetDomain\Dog\DogBreedList;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DogBreedController extends AbstractController
{
    /**
     * @Route("/api/v1/dog/breeds", methods={"GET"}, name="dog_breeds")
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/dog/breeds",
     *   tags={"Dog"},
     *   summary="Dog breed list",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "siberian-husky": "Сибирский хаски",
     *           "samoyed": "Самоед"
     *          }
     *       }
     *    )
     * )
     */
    public function getBreedList(): JsonResponse
    {
        return new JsonResponse(DogBreedList::getAll(), Response::HTTP_OK);
    }
}
