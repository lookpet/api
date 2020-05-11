<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class TempRedirectController extends AbstractController
{
    /**
     * @Route("/")
     *
     * @return RedirectResponse
     */
    public function redirectToReactApp(): RedirectResponse
    {
        return new RedirectResponse('http://look.pet:3002');
    }
}
