<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\UserFollower;
use App\PetDomain\VO\Uuid;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\UserFollower
 * @group unit
 */
final class UserFollowerTest extends TestCase
{
    private const ID = 'id';
    private const USER_ID = 'user-id';
    private const USER_SLUG = 'user-id';
    private const FOLLOWER_ID = 'follower-id';
    private const FOLLOWER_SLUG = 'follower-slug';

    public function testGettersSetters(): void
    {
        $user = new User(self::USER_ID, self::USER_SLUG);
        $follower = new User(self::FOLLOWER_ID, self::FOLLOWER_SLUG);
        $userFollower = new UserFollower(new Uuid(self::ID), $user, $follower);
        self::assertTrue($user->equals($userFollower->getUser()));
        self::assertTrue($follower->equals($userFollower->getFollower()));
    }
}
