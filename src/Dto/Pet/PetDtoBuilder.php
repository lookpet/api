<?php

namespace App\Dto\Pet;

use App\Repository\MediaRepositoryInterface;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PetDtoBuilder
{
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function build(Request $request, ?string $id = null): PetDto
    {
        $petDto = new PetDto();
        $this->setId($petDto, $id);

        if (!$request->request->has('type') || empty($request->request->get('type'))) {
            throw new \RuntimeException('Empty type', Response::HTTP_BAD_REQUEST);
        }
        $petDto->setName($request->request->get('name'));

        $petDto->setType($request->request->get('type'));

        if (!$request->request->has('slug')) {
            $slug = (new Slugify())->slugify(
                implode('-', [
                    $request->request->get('name'),
                    random_int(1000, 1000000),
                ])
            );
            $request->request->get('slug', $slug);
        }

        $petDto->setSlug($request->request->get('slug'));

        if ($request->request->has('city')) {
            $petDto->setCity($request->request->get('city'));

            if ($request->request->has('placeId')) {
                $petDto->setPlaceId($request->request->get('placeId'));
            }
        }

        if ($request->request->has('breed')) {
            $petDto->setBreed($request->request->get('breed'));
        }

        if ($request->request->has('price')) {
            $petDto->setPrice($request->request->get('price'));
        }

        if ($request->request->has('fatherName')) {
            $petDto->setFatherName($request->request->get('fatherName'));
        }
        if ($request->request->has('motherName')) {
            $petDto->setMotherName($request->request->get('motherName'));
        }

        if ($request->request->has('color')) {
            $petDto->setColor($request->request->get('color'));
        }

        if ($request->request->has('about')) {
            $petDto->setAbout($request->request->get('about'));
        }

        if ($request->request->has('eyeColor')) {
            $petDto->setEyeColor($request->request->get('eyeColor'));
        }

        if ($request->request->has('dateOfBirth')) {
            try {
                $dateOfBirth = new \DateTime($request->request->get('dateOfBirth'));
            } catch (\Exception $exception) {
                $dateOfBirth = null;
            }
            $petDto->setDateOfBirth($dateOfBirth);
        }

        if ($request->request->has('gender')) {
            $petDto->setGender($request->request->get('gender'));
        }

        $petDto->setIsLookingForOwner($this->isTrue($request, 'isLookingForNewOwner'));
        $petDto->setIsFree($this->isTrue($request, 'isFree'));
        $petDto->setIsSold($this->isTrue($request, 'isSold'));
        $this->setMedia($request, $petDto);

        return $petDto;
    }

    private function isTrue(Request $request, string $attribute): bool
    {
        return $request->request->has($attribute) && in_array($request->request->get($attribute), ['true', true]);
    }

    private function setId(PetDto $petDto, ?string $id): void
    {
        if ($id === null) {
            $id = Uuid::uuid4()->toString();
        }
        $petDto->setId($id);
    }

    private function setMedia(Request $request, PetDto $petDto): void
    {
        if ($request->request->has('media')) {
            $petMedia = [];
            $mediaCollection = $request->request->get('media');
            if (is_string($mediaCollection)) {
                $mediaCollection = [$mediaCollection];
            }
            foreach ($mediaCollection as $mediaId) {
                $media = $this->mediaRepository->findById($mediaId);
                if ($media === null) {
                    continue;
                }
                $petMedia[] = $media;
            }
            $petDto->setMedia(...$petMedia);
        }
    }
}
