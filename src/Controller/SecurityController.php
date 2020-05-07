<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    /**
     * @Route("/api/v1/authentication/login", name="api_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $activeToken = $this->getUser()->getActiveApiToken();

        if ($activeToken === null) {
            $activeToken = new ApiToken($this->getUser());
        }

        return $this->json([
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
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => 'User already exists',
            ], Response::HTTP_BAD_REQUEST);
        }

        $apiToken = new ApiToken($user);
        $entityManager->persist($apiToken);
        $entityManager->flush();

        return new JsonResponse(
            [
                'token' => $apiToken->getToken(),
                'expires_at' => $apiToken->getExpiresAt(),
            ]
        );
    }
}
