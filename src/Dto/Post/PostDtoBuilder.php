<?php

namespace App\Dto\Post;

use App\Entity\User;
use App\Repository\MediaRepositoryInterface;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

final class PostDtoBuilder implements PostDtoBuilderInterface
{
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function build(Request $request, User $user): PostDto
    {
        $id = Uuid::uuid4()->toString();
        if (!$request->request->has('id')) {
            $id = $request->request->get('id');
        }

        $postDto = new PostDto($id, $user);

        if (!$request->request->has('slug')) {
            $slug = (new Slugify())->slugify(
                implode('-', [
                    random_int(1000, 9999999),
                ])
            );
            $request->request->set('slug', $slug);
        }

        $postDto->setSlug($request->request->get('slug'));

        return $postDto;
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

    private function setMedia(Request $request, PostDto $postDto): void
    {
        if ($request->request->has('media')) {
            $postMedia = [];
            $mediaCollection = $request->request->get('media');
            if (is_string($mediaCollection)) {
                $mediaCollection = [$mediaCollection];
            }
            foreach ($mediaCollection as $mediaId) {
                $media = $this->mediaRepository->findById($mediaId);
                if ($media === null) {
                    continue;
                }
                $postMedia[] = $media;
            }
            $postDto->setMedia(...$postMedia);
        }
    }
}
