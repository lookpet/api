<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\PetDomain\VO\Gender;
use App\PetDomain\VO\Limit;
use App\PetDomain\VO\Offset;
use App\PetDomain\VO\PageNumber;
use App\Repository\PetRepository;
use App\Service\PetResponseBuilderInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetSearchController extends AbstractController
{
    private PetResponseBuilderInterface $petResponseBuilder;

    public function __construct(PetResponseBuilderInterface $petResponseBuilder)
    {
        $this->petResponseBuilder = $petResponseBuilder;
    }

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
        $gender = null;

        if ($request->query->has('gender') && in_array(
            $request->query->get('gender'), Gender::ALL
            )) {
            $gender = new Gender($request->query->get('gender'));
        }

        $isLookingForNewOwner = null;
        if ($request->query->has('isLookingForNewOwner') && in_array(
                $request->query->get('isLookingForNewOwner'),
                ['true', 'false']
            )) {
            $isLookingForNewOwner = $request->query->get('isLookingForNewOwner') === 'true';
        }

        $pets = $petRepository->findBySearch(
            $request->query->get('breed'),
            $request->query->get('type'),
            $request->query->get('city'),
            $isLookingForNewOwner,
            $gender,
            new Offset(
                new PageNumber(
                    (int) $request->get('page')
                ),
                new Limit()
            )
        );

        return $this->petResponseBuilder->build($this->getUser(), ...$pets);
    }
}
