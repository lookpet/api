<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/api/v1/user/notifications", methods={"GET"}, name="user_notifications")
     * @return JsonResponse
     */
    public function getNotifications(): JsonResponse
    {
        return new JsonResponse([
            'hello'
        ]);
    }
}