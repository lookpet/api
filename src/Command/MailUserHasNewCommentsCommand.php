<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use App\Service\Notification\EmailUserNewCommentsNotifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailUserHasNewCommentsCommand extends Command
{
    private const DESCRIPTION = 'Send email if user has new comments';
    protected static $defaultName = 'mail:user-new-comments';

    private UserRepositoryInterface $userRepository;
    private EmailUserNewCommentsNotifier $emailUserNewCommentsNotifier;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailUserNewCommentsNotifier $emailUserNewCommentsNotifier
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->emailUserNewCommentsNotifier = $emailUserNewCommentsNotifier;
    }

    protected function configure()
    {
        $this->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findUsersToNotifyNewPetComments();
        foreach ($users as $user) {
            $this->emailUserNewCommentsNotifier->notify($user);
            $this->userRepository->updateNotificationDate($user);
        }

        return 0;
    }
}
