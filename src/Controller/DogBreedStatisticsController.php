<?php

declare(strict_types=1);

namespace App\Controller;

use App\PetDomain\Dog\DogBreedList;
use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DogBreedStatisticsController extends AbstractController
{
    /**
     * @Route("/api/v1/dog/breeds/exist", methods={"GET"}, name="public_dog_breeds_exist")
     *
     * @return JsonResponse
     */
    public function getBreedList(PetRepository $petRepository): JsonResponse
    {
        return new JsonResponse(DogBreedList::getAll(), Response::HTTP_OK);
    }
}
