<?php

declare(strict_types=1);

namespace App\Controller\Pet;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Dto\Pet\PetDto;
use App\Dto\Pet\PetDtoBuilderInterface;
use App\Entity\Pet;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Slug;
use App\Repository\PetRepository;
use App\Repository\UserEventRepositoryInterface;
use App\Service\PetResponseBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetController extends AbstractController
{
    private PetResponseBuilderInterface $petResponseBuilder;
    private PetDtoBuilderInterface $petDtoBuilder;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        PetResponseBuilderInterface $petResponseBuilder,
        PetDtoBuilderInterface $petDtoBuilder,
        EntityManagerInterface $entityManager,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->petResponseBuilder = $petResponseBuilder;
        $this->petDtoBuilder = $petDtoBuilder;
        $this->entityManager = $entityManager;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/pet", methods={"POST"}, name="pet_create")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/pet",
     *   tags={"Pet"},
     *   summary="Lets user to create new pet profile",
     *   description="",
     *   operationId="petCreate",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=PetDto::class))
     *   ),
     *   @SWG\Response(response=400, description="User already exist; empty email or password",
     *     examples={
     *      "application/json": {
     *          "message":"Pet with same slug already exist"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "type": "dog",
     *           "slug": "fedor1",
     *           "name": "Fedor",
     *           "city": "Moscow",
     *           "breed": "Siberian Husky",
     *           "fatherName": "Husky Haven Lord Of The Dance",
     *           "motherName": "Husky Haven Princess",
     *           "color": "Gray and white",
     *           "eyeColor": "Gray and white",
     *               "dateOfBirth": {
     *               "date": "2019-01-01 00:00:00.000000",
     *               "timezone_type": 3,
     *               "timezone": "Europe/London"
     *           },
     *           "about": null,
     *           "gender": "male",
     *           "createdAt": {
     *               "date": "2020-05-18 16:41:33.000000",
     *               "timezone_type": 3,
     *               "timezone": "Europe/London"
     *           },
     *           "isLookingForNewOwner": false,
     *           "user": {
     *              "slug": "1jesj0h3nm6l5",
     *              "firstName": "Princess",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": "https://dev-lookpet.s3.eu-central-1.amazonaws.com/pets/uploads/image-2020-04-05-16-48-56-5ec2acb4894d98.68714269.png"
     *           }
     *          }
     *       }
     *    )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $petDto = $this->petDtoBuilder->build($request);
            $pet = new Pet(
                $petDto->getType(),
                $petDto->getSlug(),
                $petDto->getId()
            );

            $pet->updateFromDto($petDto, $this->getUser());

            $this->entityManager->persist($pet);
            $this->entityManager->flush();
            $this->userEventRepository->log(
                new EventType(EventType::PET_CREATE),
                $this->getUser(),
                $this->requestUtmBuilder->build($request),
                EventContext::createByPet($pet)
            );

            return new JsonResponse($pet);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        }
    }

    /**
     * @Route("/api/v1/pet/{slug}", methods={"POST"}, name="pet_update")
     *
     * @param string $slug
     * @param Request $request
     * @param PetRepository $petRepository
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/pet/{slug}",
     *   tags={"Pet"},
     *   summary="Lets user to update pet profile",
     *   description="",
     *   operationId="petUpdate",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=PetDto::class))
     *   ),
     *   @SWG\Response(response=400, description="User already exist; empty email or password",
     *     examples={
     *      "application/json": {
     *          "message":"Pet with same slug already exist"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "type": "dog",
     *           "slug": "fedor1",
     *           "name": "Fedor",
     *           "city": "Moscow",
     *           "breed": "Siberian Husky",
     *           "color": "Gray and white",
     *           "eyeColor": "Gray and white",
     *               "dateOfBirth": {
     *               "date": "2019-01-01 00:00:00.000000",
     *               "timezone_type": 3,
     *               "timezone": "Europe/London"
     *           },
     *           "about": null,
     *           "gender": "true",
     *           "createdAt": {
     *               "date": "2020-05-18 16:41:33.000000",
     *               "timezone_type": 3,
     *               "timezone": "Europe/London"
     *           },
     *           "isLookingForNewOwner": false,
     *           "user": {
     *              "slug": "1jesj0h3nm6l5",
     *              "firstName": "Princess",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": "https://dev-lookpet.s3.eu-central-1.amazonaws.com/pets/uploads/image-2020-04-05-16-48-56-5ec2acb4894d98.68714269.png"
     *           }
     *          }
     *       }
     *    )
     * )
     */
    public function update(string $slug, Request $request, PetRepository $petRepository): JsonResponse
    {
        try {
            $pet = $petRepository->findOneBy([
                'slug' => $slug,
                'isDeleted' => false,
            ]);

            if ($pet === null) {
                return new JsonResponse([
                    'message' => 'Pet not exist',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($pet->getUser() !== null && !$pet->getUser()->equals($this->getUser())) {
                return new JsonResponse([
                    'message' => 'Wrong user',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $petDto = $this->petDtoBuilder->build($request, $pet->getId());
            $pet->updateFromDto($petDto, $this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();

            return new JsonResponse(
                $pet
            );
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/v1/pet/{slug}", methods={"DELETE"}, name="delete_pet_slug")
     *
     * @param string $slug
     * @param PetRepository $petRepository
     *
     * @return JsonResponse
     */
    public function delete(string $slug, PetRepository $petRepository): JsonResponse
    {
        $pet = $petRepository->findOneBy([
            'slug' => $slug,
            'isDeleted' => false,
        ], [
            'createdAt' => 'desc',
        ]);

        if ($pet === null) {
            return new JsonResponse([
                'message' => 'No pet was found',
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->getDoctrine()->getManager()->remove($pet);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/api/v1/pet/{slug}", methods={"GET"}, name="public_pet_slug")
     *
     * @param string $slug
     * @param PetRepository $petRepository
     *
     * @return JsonResponse
     */
    public function getBySlug(string $slug, PetRepository $petRepository): JsonResponse
    {
        if (!$slug) {
            return new JsonResponse([
                'message' => 'No slug was sent',
            ], Response::HTTP_BAD_REQUEST);
        }

        $pet = $petRepository->findBySlug(new Slug($slug));

        if ($pet === null) {
            return new JsonResponse([
                'message' => 'No pet was found',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->petResponseBuilder->buildForOnePet($pet, $this->getUser());
    }

    /**
     * @Route("/api/v1/pets", methods={"GET"}, name="public_pet_pets")
     *
     * @SWG\Get(path="/api/v1/pets",
     *   tags={"Pet"},
     *   summary="pet all profiles feed",
     *   description="",
     *   produces={"application/json"},
     *
     *   @SWG\Parameter(
     *     name="p",
     *     description="page",
     *     in="query",
     *     type="string",
     *   ),
     *
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           {"pets list"}
     *     }
     *       }
     *    )
     * )
     *
     * @param PetRepository $petRepository
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(PetRepository $petRepository, Request $request): JsonResponse
    {
        $page = $request->get('p', 1);
        $limit = 10;
        $offset = $page === 1 ? 0 : $limit * $page;
        $pets = $petRepository->findBy([], [
            'updatedAt' => 'desc',
        ], $limit, $offset);

        $filterWithNoMedia = [];
        foreach ($pets as $pet) {
            if ($pet->hasMedia()) {
                $filterWithNoMedia[] = $pet;
            }
        }

        return $this->petResponseBuilder->build($this->getUser(), ...$filterWithNoMedia);
    }
}
