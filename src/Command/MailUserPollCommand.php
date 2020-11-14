<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\MailPollMessage;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MailUserPollCommand extends Command
{
    private const DESCRIPTION = 'Send email for user poll';
    protected static $defaultName = 'mail:user-poll';

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
        $users = $this->userRepository->findUsersToNotifyPoll();
        foreach ($users as $user) {
            $mailUserPollMessage = new MailPollMessage($user->getUuid());
            $this->messageBus->dispatch($mailUserPollMessage);
            $this->userRepository->updateNotificationAfterDate($user);
        }

        return 0;
    }
}
