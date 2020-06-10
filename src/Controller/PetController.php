<?php

namespace App\Controller;

use App\Dto\PetDto;
use App\Entity\Media;
use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Service\PetResponseBuilder;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PetController extends AbstractController
{
    private FilesystemInterface $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
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
            $type = $request->request->get('type');
            $name = $request->request->get('name');

            $pet = new Pet($type, $slug, $name, null, $this->getUser());

            if ($request->request->has('city')) {
                $pet->setCity($request->request->get('city'));
            }

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
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
                $pet->setEyeColor($request->request->get('color'));
            }

            if ($request->request->has('dateOfBirth')) {
                try{
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
                $pet->setIsLookingForOwner($request->request->get('isLookingForNewOwner'));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();

            $this->setPhotoIfExists($request, $pet);

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
            ]);

            if ($pet === null) {
                return new JsonResponse([
                    'message' => 'Pet not exist',
                ], Response::HTTP_BAD_REQUEST);
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
            }

            $this->setPhotoIfExists($request, $pet);

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
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
                try{
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
                $pet->setIsLookingForOwner($request->request->get('isLookingForNewOwner'));
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
     * @return JsonResponse
     */
    public function delete(string $slug, PetRepository $petRepository): JsonResponse
    {
        $pet = $petRepository->findOneBy([
            'slug' => $slug,
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
        ], [
            'createdAt' => 'desc',
        ]);

        if ($pet === null) {
            return new JsonResponse([
                'message' => 'No pet was found',
            ], Response::HTTP_BAD_REQUEST);
        }

        return PetResponseBuilder::buildSingle($pet, $this->getUser());
    }

    /**
     * @Route("/api/v1/pets", methods={"GET"}, name="public_pet_pets")
     *
     * @param PetRepository $petRepository
     *
     * @return JsonResponse
     */
    public function search(PetRepository $petRepository): JsonResponse
    {
        $pets = $petRepository->findBy([], [
            'updatedAt' => 'desc',
        ]);

        return PetResponseBuilder::buildResponse($pets, $this->getUser());
    }

    private function setPhotoIfExists(Request $request, Pet $pet): void
    {
        if (!$request->files->has('photo')) {
            return;
        }

        $newPhotos = $request->files->get('photo');

        if (count($newPhotos) === 0) {
            return;
        }
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($newPhotos as $newPhoto) {
            $newFile = $this->uploadFile($newPhoto);
            $media = new Media();
            $media->setPublicUrl($newFile);
            $media->setUser($this->getUser());
            $media->setSize('original');
            $pet->addMedia($media);
            $entityManager->persist($media);
            $entityManager->persist($pet);
            $entityManager->flush();
        }
    }

    private function uploadFile(File $file, string $destination = null): string
    {
        if ($destination === null) {
            $destination = '/pets/uploads/';
        }

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'rb');
        $this->filesystem->write(
            $destination . $newFilename,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $destination . $newFilename;
    }
}
