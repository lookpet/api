<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function findBySlug(string $slug): User;

    public function findByEmail(string $email): ?User;
}
