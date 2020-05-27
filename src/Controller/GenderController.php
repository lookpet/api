<?php

declare(strict_types=1);


namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\PetDomain\Genders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GenderController extends AbstractController
{
    /**
     * @Route("/api/v1/gender/list", methods={"GET"}, name="public_genders")
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/gender/list",
     *   tags={"Gender"},
     *   summary="Gender list",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "female", "male"
     *          }
     *       }
     *    )
     * )
     */
    public function getBreedList(): JsonResponse
    {
        return new JsonResponse(Genders::getAll(), Response::HTTP_OK);
    }
}