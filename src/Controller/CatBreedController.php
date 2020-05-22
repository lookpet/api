<?php

declare(strict_types=1);

namespace App\Controller;

use App\PetDomain\Cat\CatBreedList;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatBreedController extends AbstractController
{
    /**
     * @Route("/api/v1/cat/breeds", methods={"GET"}, name="public_cat_breeds")
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/cat/breeds",
     *   tags={"Cat"},
     *   summary="cat breed list",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "maine-coon": "мейн-кун",
     *           "scottish-fold": "шотландская вислоухая кошка"
     *          }
     *       }
     *    )
     * )
     */
    public function getBreedList(): JsonResponse
    {
        return new JsonResponse(CatBreedList::getAll(), Response::HTTP_OK);
    }
}
