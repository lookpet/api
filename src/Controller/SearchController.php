<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PetRepository;
use App\Service\PetResponseBuilder;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SearchController extends AbstractController
{
    /**
     * @Route("/api/v1/search/pet", methods={"GET"}, name="search_pet")
     *
     * @param Request $request
     * @param PetRepository $petRepository
     *
     * @SWG\Get(path="/api/v1/search/pet",
     *   tags={"Search"},
     *   summary="Pet search and filter",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Parameter(
     *     name="breed",
     *     in="query",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="isLookingForOwner",
     *     in="query",
     *     type="boolean",
     *   ),
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          }
     *       }
     *    )
     * )
     *
     * @return JsonResponse
     */
    public function search(Request $request, PetRepository $petRepository): JsonResponse
    {
        $pets = $petRepository->findBySearch(
            $request->query->get('breed'),
            $request->query->get('type'),
            $request->query->get('city'),
            (bool) $request->query->get('isLookingForOwner'),
        );

        return PetResponseBuilder::buildResponse($pets, $this->getUser());
    }
}
