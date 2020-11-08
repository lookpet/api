<?php

namespace App\MessageHandler;

use App\Message\MailNewCommentsMessage;
use App\Repository\UserRepositoryInterface;
use App\Service\Notification\WelcomeEmailNotifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MailWelcomeMessageHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private WelcomeEmailNotifier $emailUserWelcomeNotifier;

    public function __construct(
        UserRepositoryInterface $userRepository,
        WelcomeEmailNotifier $emailUserWelcomeNotifier
    ) {
        $this->userRepository = $userRepository;
        $this->emailUserWelcomeNotifier = $emailUserWelcomeNotifier;
    }

    public function __invoke(MailNewCommentsMessage $mailNewCommentsMessage)
    {
        $user = $this->userRepository->findByUuid($mailNewCommentsMessage->getUuid());
        if ($user->canSendNotification()) {
            $this->emailUserWelcomeNotifier->notify($user);
            $this->userRepository->updateNotificationDate($user);
            $this->userRepository->updateNotificationAfterDate($user);
        }
    }
}
