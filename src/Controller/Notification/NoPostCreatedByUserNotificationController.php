<?php

declare(strict_types=1);

namespace App\Controller\Notification;

use App\EmailTemplates\EmailTemplateDto;
use App\PetDomain\VO\EmailRecipient;
use App\Repository\UserRepositoryInterface;
use App\Service\EmailTemplateSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class NoPostCreatedByUserNotificationController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private EmailTemplateSenderInterface $emailTemplateSender;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailTemplateSenderInterface $emailTemplateSender,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->emailTemplateSender = $emailTemplateSender;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/user/notify/no-post", methods={"GET"}, name="public_notify_no_post")
     *
     * @return JsonResponse
     */
    public function sendNoPost(): JsonResponse
    {
        die();
        foreach ($users as $user) {
            if ($user->hasNotificationSentToday()) {
                $emails[] = $user->getEmail();

                if (false && !$user->hasNotificationSentToday() && !$user->isLookPetUser() && (bool) $_ENV['IS_SEND_EMAIL_NOTIFICATIONS'] === true) {
                    $this->emailTemplateSender->send(new EmailTemplateDto(
                        EmailRecipient::create(
                            $user->getEmail(),
                            $user->getName()
                        ),
                        'Кое-что новенькое у вас в ленте look.pet',
                        (int) 1801577
                    ));
                    $user->updateNotificationDate();
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            }
        }

        return new JsonResponse($emails);
    }
}
