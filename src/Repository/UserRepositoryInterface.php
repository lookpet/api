<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function findBySlug(string $slug): User;

    public function findByEmail(string $email): ?User;

    /**
     * @return User[]
     */
    public function findUsersWithNoPets(): array;

    public function findUsersToNotifyNoPets(): iterable;

    public function findUsersToNotifyNewPetComments(): iterable;

    public function findUsersToNotifyPoll(): iterable;

    public function updateNotificationDate(User $user): void;
}
