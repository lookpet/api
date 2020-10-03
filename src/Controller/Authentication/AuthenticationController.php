<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Dto\AuthenticationUserLoginDto;
use App\Dto\AuthenticationUserRegistrationDto;
use App\EmailTemplates\EmailTemplateDto;
use App\Entity\ApiToken;
use App\Entity\User;
use App\PetDomain\VO\EmailRecipient;
use App\Repository\UserRepository;
use App\Service\EmailTemplateSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthenticationController extends AbstractController
{
    private UserRepository $userRepository;
    private ValidatorInterface $validator;

    public function __construct(UserRepository $userRepository, ValidatorInterface $validator)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/v1/authentication/login", methods={"POST"}, name="api_login")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/authentication/login",
     *   tags={"Authentication"},
     *   summary="Lets user to login",
     *   description="",
     *   operationId="login",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=AuthenticationUserLoginDto::class))
     *   ),
     *   @SWG\Response(response=400, description="Invalid email or password",
     *     examples={
     *      "application/json": {
     *          "message":"Invalid email or password"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          "user": {
     *              "slug": "6r7civ9rl4gf",
     *              "firstName": "Svetoslav",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": null
     *           },
     *           "token": "bd76fbd73fe552c0600437d83aa56b0c0b78eb22117b8582fecb9daa6fd5e308d994f64e05aa0538b49c07acd10ac27630c82a031a985e60053e5ec1",
     *           "expires_at": {
     *              "date": "2020-05-25 16:23:41.290766",
     *              "timezone_type": 3,
     *              "timezone": "Europe/London"
     *              }
     *           }
     *       }
     *    )
     * )
     */
    public function login(Request $request,
                          UserPasswordEncoderInterface $passwordEncoder,
                          EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->request->get('email');

        if ($email === null) {
            return new JsonResponse([
                'message' => 'Empty email',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$this->isValidEmail($email)) {
            new JsonResponse(
                [
                    'message' => 'Invalid email address',
                ], Response::HTTP_BAD_REQUEST
            );
        }

        $password = $request->request->get('password');

        if ($password === null) {
            return new JsonResponse([
                'message' => 'Empty password',
            ], 400);
        }

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if ($user === null) {
            return new JsonResponse([
                'message' => 'Invalid email or password',
            ], 400);
        }

        if (!$passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse([
                'message' => 'Invalid email or password',
            ], 400);
        }

        $activeToken = $user->getActiveApiToken();

        if ($activeToken === null) {
            $activeToken = new ApiToken($user);
            $entityManager->persist($activeToken);
            $entityManager->flush();
        }

        return $this->json([
            'user' => $user,
            'token' => $activeToken->getToken(),
            'expires_at' => $activeToken->getExpiresAt(),
        ]);
    }

    /**
     * @Route("/api/v1/authentication/register", methods={"POST"}, name="api_register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param EmailTemplateSenderInterface $emailTemplateSender
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/authentication/register",
     *   tags={"Authentication"},
     *   summary="Lets user to create new account",
     *   description="",
     *   operationId="register",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=AuthenticationUserRegistrationDto::class))
     *   ),
     *   @SWG\Response(response=400, description="User already exist; empty email or password",
     *     examples={
     *      "application/json": {
     *          "message":"User already exist"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          "user": {
     *              "slug": "6r7civ9rl4gf",
     *              "firstName": "Svetoslav",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": null
     *           },
     *           "token": "bd76fbd73fe552c0600437d83aa56b0c0b78eb22117b8582fecb9daa6fd5e308d994f64e05aa0538b49c07acd10ac27630c82a031a985e60053e5ec1",
     *           "expires_at": "2020-05-25T16:23:41+01:00"
     *           }
     *       }
     *    )
     * )
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        EmailTemplateSenderInterface $emailTemplateSender
    ): JsonResponse {
        try {
            if (!$request->request->has('email')) {
                return new JsonResponse([
                    'message' => 'Empty email',
                ], Response::HTTP_BAD_REQUEST);
            }

            if (!$request->request->has('password')) {
                return new JsonResponse([
                    'message' => 'Empty password',
                ], Response::HTTP_BAD_REQUEST);
            }

            $email = $request->request->get('email');

            if (!$this->isValidEmail($email)) {
                return new JsonResponse(
                    [
                        'message' => 'Invalid email address',
                    ], Response::HTTP_BAD_REQUEST
                );
            }

            $password = $request->request->get('password');

            if (!$this->isValidPassword($password)) {
                return new JsonResponse(
                    [
                        'message' => 'Password too short min length is 6',
                    ], Response::HTTP_BAD_REQUEST
                );
            }

            /** @todo use form validation with DTO */
            $user = new User(
                null,
                $request->request->get('firstName')
            );
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $userExist = $this->userRepository->findOneBy([
                'email' => $email,
            ]);

            if ($userExist !== null) {
                return new JsonResponse([
                    'message' => 'User already exist',
                ], Response::HTTP_BAD_REQUEST);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            if (!$user->isLookPetUser()) {
                $emailTemplateSender->send(new EmailTemplateDto(
                    EmailRecipient::create(
                        $user->getEmail(),
                        $user->getName()
                    ),
                    'Добро пожаловать на look.pet',
                    $_ENV['MJ_TEMPLATE_WELCOME']
                ));
            }
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $apiToken = new ApiToken($user);
        $entityManager->persist($apiToken);
        $entityManager->flush();

        return new JsonResponse(
            [
                'user' => $user,
                'token' => $apiToken->getToken(),
                'expires_at' => $apiToken->getExpiresAt(),
            ]
        );
    }

    private function isValidEmail(string $email): bool
    {
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email address';

        $errors = $this->validator->validate(
            $email,
            $emailConstraint
        );

        return count($errors) === 0;
    }

    private function isValidPassword(string $password): bool
    {
        $passwordConstraint = new Assert\Length([
            'min' => 6,
        ]);

        $errors = $this->validator->validate(
            $password,
            $passwordConstraint
        );

        return count($errors) === 0;
    }
}