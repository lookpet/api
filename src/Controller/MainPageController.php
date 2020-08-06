<?php


namespace App\Controller;

use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="main_page")
     *
     * @return JsonResponse
     */
    public function main():JsonResponse
    {
        return new JsonResponse();
    }
}