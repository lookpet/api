<?php

namespace App\Controller;

use App\Dto\AuthenticationUserLoginDto;
use App\Dto\AuthenticationUserRegistrationDto;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class SecurityController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            ], 400);
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
        EntityManagerInterface $entityManager): JsonResponse
    {
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

            /** @todo use form validation with DTO */
            $user = new User();
            $user->setFirstName($request->request->get('firstName'));
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

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
}
