<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\MailPollMessage;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\Notification\EmailUserPollNotifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MailPollMessageHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private EmailUserPollNotifier $emailNoPostCreatedNotifier;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailUserPollNotifier $emailNoPostCreatedNotifier,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->emailNoPostCreatedNotifier = $emailNoPostCreatedNotifier;
        $this->userEventRepository = $userEventRepository;
    }

    public function __invoke(MailPollMessage $mailNoPetMessage)
    {
        $user = $this->userRepository->findByUuid($mailNoPetMessage->getUuid());
        if ($user->canSendNotification()) {
            $this->emailNoPostCreatedNotifier->notify($user);
            $this->userRepository->updateNotificationDate($user);
            $this->userEventRepository->log(
                new EventType(EventType::POLL_NOTIFICATION),
                $user,
                new Utm()
            );
        }
    }
}
