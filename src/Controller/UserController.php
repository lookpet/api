<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $this->setPhotoIfExists($request, $user);
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
            'avatar' => $user->getAvatarUrl(),
        ]);
    }

    private function setPhotoIfExists(Request $request, User $user):void
    {
        $newPhoto = $request->files->get('photo');
        $entityManager = $this->getDoctrine()->getManager();
        if ($newPhoto) {
            $newFile = $this->uploadFile($newPhoto);
            $media = new Media();
            $media->setPublicUrl('/uploads/' . $newFile);
            $media->setUser($user);
            $media->setSize('original');
            $entityManager->persist($media);
            $entityManager->flush();
        }
    }

    private function uploadFile(File $file, string $destination = null): string
    {
        if ($destination === null) {
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
        }

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $file->move($destination, $newFilename);

        return $newFilename;
    }
}