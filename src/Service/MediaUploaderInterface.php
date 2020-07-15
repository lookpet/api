<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

interface MediaUploaderInterface
{
    /**
     * @param UserInterface $user
     * @param Request $request
     *
     * @return Media[]
     */
    public function uploadByRequest(UserInterface $user, Request $request): iterable;
}
