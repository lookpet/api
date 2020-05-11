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
    public function redirectToReactApp()
    {
        return $this->render('base.html.twig');
    }
}
