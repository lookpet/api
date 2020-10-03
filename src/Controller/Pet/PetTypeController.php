<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\PetDomain\PetTypes;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetTypeController extends AbstractController
{
    /**
     * @Route("/api/v1/types", methods={"GET"}, name="public_pet_types")
     *
     * @return JsonResponse
     *
     *  @SWG\Get(path="/api/v1/types",
     *   tags={"Pet"},
     *   summary="Pet types list",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "dog": "собака",
     *           "cat": "кот/кошка"
     *          }
     *       }
     *    )
     * )
     */
    public function getTypes(): JsonResponse
    {
        return new JsonResponse(
            PetTypes::getList(),
            Response::HTTP_OK
        );
    }
}
