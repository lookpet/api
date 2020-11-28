<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMessage;

interface UserMessageRepositoryInterface
{
    /**
     * @param User $from
     * @param User $to
     *
     * @return UserMessage[]
     */
    public function getChatMessages(User $from, User $to): iterable;

    public function save(UserMessage $userMessage): void;

    public function remove(UserMessage $userMessage): void;
}
