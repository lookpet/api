<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;;
use Swagger\Annotations as SWG;

class FaceBookController extends AbstractController
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
     * @Route("/api/v1/authentication/facebook", methods={"POST"}, name="public_authentication_facebook")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function facebook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->request->has('userID')) {
            $user = null;

            if ($request->request->has('email')) {
                $user = $this->userRepository->findOneBy([
                    'email' => $request->request->get('email'),
                ]);
            }

            if ($user === null) {
                $user = $this->userRepository->findOneBy([
                    'provider' => 'facebook',
                    'providerId' => $request->request->get('userID'),
                ]);
            }

            if ($user === null) {
                $user = new User();
                $user->setEmail($request->request->get('email'));
                $user->setName($request->request->get('name'));
                $name = explode(' ', $request->request->get('name'));
                $user->setFirstName(isset($name[0]) ? $name[0] : null);
                $user->setLastName(isset($name[1]) ? $name[1] : null);
                $user->setProvider('facebook');
                $user->setProviderId($request->request->get('userID'));
            }

            $user->setProviderLastResponse($request->getContent());
            $entityManager->persist($user);
            $entityManager->flush();

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

        return new JsonResponse([], Response::HTTP_NOT_FOUND);
    }
}