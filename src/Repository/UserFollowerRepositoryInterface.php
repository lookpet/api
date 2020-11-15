<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserFollower;

interface UserFollowerRepositoryInterface
{
    public function getUserFollower(User $user, User $follower): ?UserFollower;

    public function save(UserFollower $userFollower): void;

    public function remove(UserFollower $userFollower): void;
}
