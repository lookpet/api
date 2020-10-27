<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\PetLike;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Slug;
use App\Repository\PetLikeRepositoryInterface;
use App\Repository\PetRepositoryInterface;
use App\Repository\UserEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetLikeController extends AbstractController
{
    private PetRepositoryInterface $petRepository;
    private PetLikeRepositoryInterface $petLikeRepository;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        PetRepositoryInterface $petRepository,
        PetLikeRepositoryInterface $petLikeRepository,
        EntityManagerInterface $entityManager,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->petRepository = $petRepository;
        $this->petLikeRepository = $petLikeRepository;
        $this->entityManager = $entityManager;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/pet/{slug}/like", methods={"POST"}, name="pet_like")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/pet/{slug}/like",
     *   tags={"Like"},
     *   summary="Lets like pets",
     *   description="",
     *   operationId="petLike",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "hasLike": true,
     *           "total": 30,
     *          }
     *       }
     *    )
     * )
     */
    public function like(string $slug, Request $request): JsonResponse
    {
        $petBySlug = $this->petRepository->findBySlug(
            new Slug($slug)
        );

        if ($petBySlug === null) {
            return new JsonResponse([
                'message' => 'Pet not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        $petLike = $this->petLikeRepository->getUserPetLike(
            $this->getUser(),
            $petBySlug
        );

        if ($petLike === null) {
            $petLike = new PetLike(
                $petBySlug,
                $this->getUser(),
                Uuid::uuid4()->toString()
            );
            $petBySlug->addLikes(
                $petLike
            );
            $this->entityManager->persist($petLike);
            $this->userEventRepository->log(
                new EventType(EventType::PET_LIKE),
                $this->getUser(),
                $this->requestUtmBuilder->build($request),
                EventContext::createByPet($petBySlug)
            );
        } else {
            $petBySlug->removeLike(...[$petLike]);
            $this->entityManager->remove($petLike);
            $this->userEventRepository->log(
                new EventType(EventType::PET_UNLIKE),
                $this->getUser(),
                $this->requestUtmBuilder->build($request),
                EventContext::createByPet($petBySlug)
            );
        }

        $this->entityManager->persist($petBySlug);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'hasLike' => $petBySlug->hasLike($this->getUser()),
                'total' => count($petBySlug->getLikes()),
            ]
        );
    }
}
