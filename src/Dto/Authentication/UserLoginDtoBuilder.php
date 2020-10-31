<?php

namespace App\Dto\Authentication;

use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserLoginDtoBuilder
{
    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    public function build(Request $request): UserLoginDto
    {
        if (!$request->request->has('email') ||
            empty($request->request->get('email'))) {
            throw new \LogicException('Empty email', Response::HTTP_BAD_REQUEST);
        }

        if (!$request->request->has('password') ||
            empty($request->request->get('password'))) {
            throw new \LogicException('Empty password', Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isValidEmail(
            $request->request->get('email')
        )) {
            throw new \LogicException('Invalid email', Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isValidPasswordLength(
            $request->request->get('password')
        )) {
            throw new \LogicException('Password too short min length is 1', Response::HTTP_BAD_REQUEST);
        }

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password')
        );
        $userLoginDto->setId(Uuid::uuid4()->toString());
        $userLoginDto->setFirstName(
            $request->request->get('firstName')
        );

        if (!$request->request->has('slug')) {
            $slug = (new Slugify())->slugify(
                implode('-', [
                    $request->request->get('firstName'),
                    random_int(1000, 1000000),
                ])
            );
            $request->request->set('slug', $slug);
        }

        $userLoginDto->setSlug($request->request->get('slug'));

        return $userLoginDto;
    }

    private function isValidEmail(string $email): bool
    {
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email';

        $errors = $this->validator->validate(
            $email,
            $emailConstraint
        );

        return count($errors) === 0;
    }

    private function isValidPasswordLength(string $password): bool
    {
        $passwordConstraint = new Assert\Length([
            'min' => 1,
        ]);

        $errors = $this->validator->validate(
            $password,
            $passwordConstraint
        );

        return count($errors) === 0;
    }
}
