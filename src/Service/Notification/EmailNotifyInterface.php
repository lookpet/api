<?php

namespace App\Service\Notification;

use App\Entity\User;

interface EmailNotifyInterface
{
    public function notify(User $user): void;
}
