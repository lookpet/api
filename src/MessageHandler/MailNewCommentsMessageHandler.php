<?php

namespace App\MessageHandler;

use App\Message\MailNewCommentsMessage;
use App\Repository\UserRepositoryInterface;
use App\Service\Notification\EmailUserNewCommentsNotifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MailNewCommentsMessageHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private EmailUserNewCommentsNotifier $emailUserNewCommentsNotifier;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailUserNewCommentsNotifier $emailUserNewCommentsNotifier
    ) {
        $this->userRepository = $userRepository;
        $this->emailUserNewCommentsNotifier = $emailUserNewCommentsNotifier;
    }

    public function __invoke(MailNewCommentsMessage $mailNewCommentsMessage)
    {
        $user = $this->userRepository->findByUuid($mailNewCommentsMessage->getUuid());
        $this->emailUserNewCommentsNotifier->notify($user);
        $this->userRepository->updateNotificationDate($user);
    }
}
