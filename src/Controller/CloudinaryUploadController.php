<?php

declare(strict_types=1);

namespace App\Controller;

use App\PetDomain\Dog\DogBreedList;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CloudinaryUploadController extends AbstractController
{
    /**
     * @Route("/api/v1/cloudinary/media", methods={"POST"}, name="public_cloudinary_upload")
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/cloudinary/media",
     *   tags={"Cloudinary"},
     *   summary="Cloudinary upload",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *
     *          }
     *       }
     *    )
     * )
     */
    public function upload(): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_OK);
    }
}
