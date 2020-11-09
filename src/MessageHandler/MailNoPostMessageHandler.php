<?php

namespace App\MessageHandler;

use App\Message\MailNoPetMessage;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\Notification\EmailNoPostCreatedNotifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MailNoPostMessageHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private EmailNoPostCreatedNotifier $emailNoPostCreatedNotifier;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailNoPostCreatedNotifier $emailNoPostCreatedNotifier,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->emailNoPostCreatedNotifier = $emailNoPostCreatedNotifier;
        $this->userEventRepository = $userEventRepository;
    }

    public function __invoke(MailNoPetMessage $mailNoPetMessage)
    {
        $user = $this->userRepository->findByUuid($mailNoPetMessage->getUuid());
        if ($user->canSendNotification()) {
            $this->emailNoPostCreatedNotifier->notify($user);
            $this->userRepository->updateNotificationDate($user);
            $this->userEventRepository->log(
                new EventType(EventType::NO_PET_NOTIFICATION),
                $user,
                new Utm()
            );
        }
    }
}
