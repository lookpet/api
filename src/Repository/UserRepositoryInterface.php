<?php

namespace App\Repository;

use App\Entity\User;
use App\PetDomain\VO\Id;

interface UserRepositoryInterface
{
    public function findByUuid(Id $uuid): ?User;

    public function findBySlug(string $slug): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @return User[]&iterable
     */
    public function findUsersToNotifyNoPets(): iterable;

    /**
     * @return User[]&iterable
     */
    public function findUsersToNotifyNewPetComments(): iterable;

    /**
     * @return User[]&iterable
     */
    public function findUsersToNotifyPoll(): iterable;

    public function updateNotificationDate(User $user): void;

    public function updateNotificationAfterDate(User $user): void;

    public function save(User $user): void;
}
