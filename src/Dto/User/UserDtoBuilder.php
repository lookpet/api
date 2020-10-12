<?php

namespace App\Dto\User;

use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

final class UserDtoBuilder
{
    /**
     * @var Slugify
     */
    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function build(Request $request, ?string $id = null): UserDto
    {
        $userDto = new UserDto();
        $this->setId($userDto, $id);

        if ($request->request->has('firstName')) {
            $userDto->setFirstName($request->request->get('firstName'));
        }

        if ($request->request->has('phone')) {
            $userDto->setPhone($request->request->get('phone'));
        }

        if ($request->request->has('description')) {
            $userDto->setDescription($request->request->get('description'));
        }

        if ($request->request->has('city')) {
            $userDto->setCity($request->request->get('city'));
        }

        if ($request->request->has('slug')) {
            $userDto->setSlug($request->request->get('slug'));
        }

        return $userDto;
    }

    private function setId(UserDto $userDto, ?string $id): void
    {
        if ($id === null) {
            $id = Uuid::uuid4()->toString();
        }
        $userDto->setId($id);
    }

    private function generateSlug(UserDto $userDto): void
    {
        $firstName = mb_strtolower($userDto->getFirstName());
        $slugEntropy = base_convert(rand(1000000000, PHP_INT_MAX), 10, 36);
        $userDto->setSlug(
            $this->slugify->slugify(implode('-', [$firstName, $slugEntropy]))
        );
    }
}
