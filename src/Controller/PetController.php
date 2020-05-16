<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Pet;
use App\Repository\PetRepository;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
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
     */
    public function create(Request $request): JsonResponse
    {
        try{
            if (!$request->request->has('type')) {
                return new JsonResponse([
                    'message' => 'Empty type',
                ], Response::HTTP_BAD_REQUEST);
            }

            $slug = $request->request->get('slug');
            $type = $request->request->get('type');
            $name = $request->request->get('name');

            $pet = new Pet($type, $slug, $name, null, $this->getUser());

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
            }

            if ($request->request->has('color')) {
                $pet->setColor($request->request->get('color'));
            }

            if ($request->request->has('eyeColor')) {
                $pet->setEyeColor($request->request->get('color'));
            }

            if ($request->request->has('dateOfBirth')) {
                $pet->setDateOfBirth(new \DateTime($request->request->get('dateOfBirth')));
            }

            if ($request->request->has('gender')) {
                $pet->setGender($request->request->get('gender'));
            }

            if ($request->request->has('isLookingForNewOwner')) {
                $pet->setGender($request->request->get('isLookingForNewOwner'));
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
     */
    public function update(string $slug, Request $request, PetRepository $petRepository): JsonResponse
    {
        try{
            if (!$request->request->has('type')) {
                return new JsonResponse([
                    'message' => 'Empty type',
                ], Response::HTTP_BAD_REQUEST);
            }

            $pet = $petRepository->findOneBy([
                'slug' => $slug,
            ]);

            if ($pet === null) {
                return new JsonResponse([
                    'message' => 'Pet not exist',
                ], Response::HTTP_BAD_REQUEST);
            }

            $type = $request->request->get('type');
            $name = $request->request->get('name');

            $pet->setType($type);
            $pet->setName($name);

            if ($request->request->has('breed')) {
                $pet->setBreed($request->request->get('breed'));
            }

            if ($request->request->has('color')) {
                $pet->setColor($request->request->get('color'));
            }

            if ($request->request->has('eyeColor')) {
                $pet->setEyeColor($request->request->get('color'));
            }

            if ($request->request->has('dateOfBirth')) {
                $pet->setDateOfBirth(new \DateTime($request->request->get('dateOfBirth')));
            }

            if ($request->request->has('gender')) {
                $pet->setGender($request->request->get('gender'));
            }

            if ($request->request->has('isLookingForNewOwner')) {
                $pet->setGender($request->request->get('isLookingForNewOwner'));
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
        ]);

        if ($pet === null) {
            return new JsonResponse([
                'message' => 'No pet was found',
            ], Response::HTTP_BAD_REQUEST);
        }
        //commit
        return new JsonResponse(
            $pet
        );
    }

    /**
     * @Route("/api/v1/pets", methods={"GET"})
     *
     * @param PetRepository $petRepository
     *
     * @return JsonResponse
     */
    public function search(PetRepository $petRepository): JsonResponse
    {
        $pets = $petRepository->findAll();

        return new JsonResponse([
            'pets' => $pets,
        ]);
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
