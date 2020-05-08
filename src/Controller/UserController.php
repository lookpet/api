<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    /**
     * @Route("/api/v1/user", methods={"POST"})
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request, UserRepository $userRepository):JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setFirstName($request->request->get('firstName'));
        $user->setPhone($request->request->get('phone'));
        $user->setDescription($request->request->get('description'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse([
            'slug' => $user->getSlug(),
            'firstName' => $user->getFirstName(),
            'phone' => $user->getPhone(),
            'description' => $user->getDescription(),
        ]);
    }
}