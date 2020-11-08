<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testNotificationDate(): void
    {
        $user = new User();
        self::assertTrue($user->canSendNotification());
        $user->updateNotificationDate(new \DateTimeImmutable('-1 day'));
        self::assertTrue($user->canSendNotification());
        $user->updateNotificationDate(new \DateTimeImmutable('now'));
        self::assertFalse($user->canSendNotification());
    }
}
