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

    /**
     * @param User $user
     *
     * @return UserMessage[]
     */
    public function getChatLastMessages(User $user): iterable;

    public function save(UserMessage $userMessage): void;

    public function remove(UserMessage $userMessage): void;

    public function readMessages(User $from, User $to): void;

    public function getChatListCount(User $user): int;

    public function getChatMessagesCount(User $from, User $to): int;
}
