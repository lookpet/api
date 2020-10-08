<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\Entity\PetComment;
use App\Repository\PetCommentRepository;
use App\Repository\PetRepository;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetCommentController extends AbstractController
{
    private PetRepository $petRepository;
    private PetCommentRepository $petCommentRepository;

    public function __construct(PetRepository $petRepository, PetCommentRepository $petCommentRepository)
    {
        $this->petRepository = $petRepository;
        $this->petCommentRepository = $petCommentRepository;
    }

    /**
     * @Route("/api/v1/pet/{slug}/comment", methods={"POST"}, name="pet_comment")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/pet/{slug}/comment",
     *   tags={"Comment"},
     *   summary="Lets comment pets",
     *   description="",
     *   operationId="petComment",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          }
     *       }
     *    )
     * )
     */
    public function comment(string $slug, Request $request): JsonResponse
    {
        $pets = $this->petRepository->findBy([
            'slug' => $slug,
        ]);

        if (count($pets) === 0) {
            return new JsonResponse([
                'message' => 'Pet not exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $pet = array_pop($pets);
        $petComment = new PetComment($this->getUser(), $request->request->get('comment'), $pet);
        $pet->addComments($petComment);
        $this->getDoctrine()->getManager()->persist($pet);
        $this->getDoctrine()->getManager()->persist($petComment);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }
}
