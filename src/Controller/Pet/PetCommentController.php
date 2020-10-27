<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\PetComment;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Slug;
use App\Repository\PetCommentRepository;
use App\Repository\PetRepository;
use App\Repository\UserEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;
    /**
     * @var RequestUtmBuilderInterface
     */
    private RequestUtmBuilderInterface $requestUtmBuilder;
    /**
     * @var UserEventRepositoryInterface
     */
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        PetRepository $petRepository,
        PetCommentRepository $petCommentRepository,
        EntityManagerInterface $entityManager,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->petRepository = $petRepository;
        $this->petCommentRepository = $petCommentRepository;
        $this->entityManager = $entityManager;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
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
        $pet = $this->petRepository->findBySlug(new Slug($slug));

        if ($pet === null) {
            return new JsonResponse([
                'message' => 'Pet not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        $petComment = new PetComment($this->getUser(), $request->request->get('comment'), $pet);
        $pet->addComments($petComment);
        $this->entityManager->persist($pet);
        $this->entityManager->persist($petComment);
        $this->entityManager->flush();

        $this->userEventRepository->log(
            new EventType(EventType::PET_COMMENT),
            $this->getUser(),
            $this->requestUtmBuilder->build($request),
            EventContext::createByPet($pet)
        );

        return new JsonResponse([
            $petComment,
        ], Response::HTTP_OK);
    }
}
