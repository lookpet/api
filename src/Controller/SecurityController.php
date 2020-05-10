<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class SecurityController extends AbstractController
{
    /**
     * @Route("/api/v1/authentication/login", name="api_login")
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function login(Request $request,
                          UserRepository $userRepository,
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

        $user = $userRepository->findOneBy([
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
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            /** @todo use form validation with DTO */
            $user = new User();
            $user->setFirstName($request->request->get('firstName'));
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

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
