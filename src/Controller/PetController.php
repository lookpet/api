<?php

namespace App\Controller;

use App\Dto\PetDto;
use App\Entity\Pet;
use App\Repository\MediaRepository;
use App\Repository\PetRepository;
use App\Service\MediaCropperInterface;
use App\Service\MediaUploaderInterface;
use App\Service\PetResponseBuilderInterface;
use Cocur\Slugify\Slugify;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetController extends AbstractController
{
    /**
     * @var PetResponseBuilderInterface
     */
    private PetResponseBuilderInterface $petResponseBuilder;
    /**
     * @var MediaUploaderInterface
     */
    private MediaUploaderInterface $mediaUploader;
    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;
    /**
     * @var MediaCropperInterface
     */
    private MediaCropperInterface $mediaCropper;

    public function __construct(
        PetResponseBuilderInterface $petResponseBuilder,
        MediaUploaderInterface $mediaUploader,
        MediaRepository $mediaRepository,
        MediaCropperInterface $mediaCropper
    ) {
        $this->petResponseBuilder = $petResponseBuilder;
        $this->mediaUploader = $mediaUploader;
        $this->mediaRepository = $mediaRepository;
        $this->mediaCropper = $mediaCropper;
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
            if (!$request->request->has('type')) {
                return new JsonResponse([
                    'message' => 'Empty type',
                ], Response::HTTP_BAD_REQUEST);
            }

            $slug = $request->request->get('slug');
            if (!$request->request->has('slug')) {
                $slugify = new Slugify();
                $slug = $slugify->slugify(
                    implode('-', [
                        $request->request->get('name'),
                        random_int(1000, 1000000),
                    ])
                );
            }

            $type = $request->request->get('type');
            $name = $request->request->get('name');

            $pet = new Pet($type, $slug, $name, null, $this->getUser());

            if ($request->request->has('city')) {
                $pet->setCity($request->request->get('city'));

                if ($request->request->has('placeId')) {
                    $pet->setPlaceId($request->request->get('placeId'));
                }
            }

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
            }

            if ($request->request->has('price')) {
                $pet->setPrice($request->request->get('price'));
            }

            if ($request->request->has('fatherName')) {
                $pet->setFatherName($request->request->get('fatherName'));
            }
            if ($request->request->has('motherName')) {
                $pet->setMotherName($request->request->get('motherName'));
            }

            if ($request->request->has('color')) {
                $pet->setColor($request->request->get('color'));
            }

            if ($request->request->has('about')) {
                $pet->setAbout($request->request->get('about'));
            }

            if ($request->request->has('eyeColor')) {
                $pet->setEyeColor($request->request->get('eyeColor'));
            }

            if ($request->request->has('dateOfBirth')) {
                try {
                    $dateOfBirth = new \DateTime($request->request->get('dateOfBirth'));
                } catch (\Exception $exception) {
                    $dateOfBirth = null;
                }
                $pet->setDateOfBirth($dateOfBirth);
            }

            if ($request->request->has('gender')) {
                $pet->setGender($request->request->get('gender'));
            }

            if ($request->request->has('isLookingForNewOwner')) {
                $isLookingForNewOwner = $request->request->get('isLookingForNewOwner') === 'true';
                $pet->setIsLookingForOwner($isLookingForNewOwner);
            }

            if ($request->request->has('isFree')) {
                $isFree = $request->request->get('isFree') === 'true';
                $pet->setIsFree($isFree);
            }

            if ($request->request->has('isSold')) {
                $isSold = $request->request->get('isSold') === 'true';
                $pet->setIsSold($isSold);
            }

            $this->setPhotoIfExists($request, $pet);
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

            if ($pet->getUser()->getId() !== $this->getUser()->getId()) {
                return new JsonResponse([
                    'message' => 'Wrong user',
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($request->request->has('type') && empty($request->request->get('type'))) {
                return new JsonResponse([
                    'message' => 'Empty type',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($request->request->has('type')) {
                $pet->setType($request->request->get('type'));
            }
            if ($request->request->has('name')) {
                $pet->setName($request->request->get('name'));
            }

            if ($request->request->has('city')) {
                $pet->setCity($request->request->get('city'));

                if ($request->request->has('placeId')) {
                    $pet->setPlaceId($request->request->get('placeId'));
                }
            }

            $this->setPhotoIfExists($request, $pet);

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
            }

            if ($request->request->has('price')) {
                $pet->setPrice($request->request->get('price'));
            }

            if ($request->request->has('fatherName')) {
                $pet->setFatherName($request->request->get('fatherName'));
            }
            if ($request->request->has('motherName')) {
                $pet->setMotherName($request->request->get('motherName'));
            }

            if ($request->request->has('color')) {
                $pet->setColor($request->request->get('color'));
            }

            if ($request->request->has('eyeColor')) {
                $pet->setEyeColor($request->request->get('eyeColor'));
            }

            if ($request->request->has('dateOfBirth')) {
                try {
                    $dateOfBirth = new \DateTime($request->request->get('dateOfBirth'));
                } catch (\Exception $exception) {
                    $dateOfBirth = null;
                }
                $pet->setDateOfBirth($dateOfBirth);
            }

            if ($request->request->has('gender')) {
                $pet->setGender($request->request->get('gender'));
            }

            if ($request->request->has('about')) {
                $pet->setAbout($request->request->get('about'));
            }

            if ($request->request->has('isLookingForNewOwner')) {
                $isLookingForNewOwner = $request->request->get('isLookingForNewOwner') === 'true';
                $pet->setIsLookingForOwner($isLookingForNewOwner);
            }

            if ($request->request->has('isFree')) {
                $isFree = $request->request->get('isFree') === 'true';
                $pet->setIsFree($isFree);
            }

            if ($request->request->has('isSold')) {
                $isSold = $request->request->get('isSold') === 'true';
                $pet->setIsSold($isSold);
            }

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
    public function search(PetRepository $petRepository, Request $request): JsonResponse
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

    private function setPhotoIfExists(Request $request, Pet $pet): void
    {
        if ($request->request->has('media')) {
            $petMedia = [];
            $mediaCollection = $request->request->get('media');
            if (is_string($mediaCollection)) {
                $mediaCollection = [$mediaCollection];
            }
            foreach ($mediaCollection as $mediaId) {
                $media = $this->mediaRepository->find($mediaId);
                if ($media === null) {
                    continue;
                }
                $petMedia[] = $media;
            }
            $pet->addMedia(...$petMedia);
        }
        $mediaPetCollection = $this->mediaUploader->uploadByRequest(
            $request, $this->getUser()
        );

        $pet->addMedia(...$mediaPetCollection);
    }
}
