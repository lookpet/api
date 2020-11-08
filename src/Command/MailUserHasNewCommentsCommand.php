<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\MailNewCommentsMessage;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MailUserHasNewCommentsCommand extends Command
{
    private const DESCRIPTION = 'Send email if user has new comments';
    protected static $defaultName = 'mail:user-new-comments';

    private UserRepositoryInterface $userRepository;
    private MessageBusInterface $messageBus;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->messageBus = $messageBus;
    }

    protected function configure()
    {
        $this->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findUsersToNotifyNewPetComments();
        foreach ($users as $user) {
            $mailNewCommentMessage = new MailNewCommentsMessage($user->getUuid());
            $this->messageBus->dispatch($mailNewCommentMessage);
            $this->userRepository->updateNotificationAfterDate($user);
        }

        return 0;
    }
}
