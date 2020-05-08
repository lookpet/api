<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PingController extends AbstractController
{
    /**
     * @Route("/ping")
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
