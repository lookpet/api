<?php

namespace Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\UserMessage;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Message;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \App\Entity\UserMessage
 */
class UserMessageTest extends TestCase
{
    private const ID = 'id';
    private const FROM_USER_ID = 'from-user-id';
    private const FROM_USER_SLUG = 'user-id';
    private const TO_USER_ID = 'to-user-id';
    private const TO_USER_SLUG = 'to-user-slug';
    private const MESSAGE = 'Hello!';

    public function testGettersSetters(): void
    {
        $fromUser = new User(self::FROM_USER_ID, self::FROM_USER_SLUG);
        $toUser = new User(self::TO_USER_ID, self::TO_USER_SLUG);
        $message = new Message(self::MESSAGE);
        $userMessage = new UserMessage(Id::create(self::ID), $fromUser, $toUser, $message);
        self::assertTrue($fromUser->equals($userMessage->getFromUser()));
        self::assertTrue($toUser->equals($userMessage->getToUser()));
        self::assertEquals($message, $userMessage->getMessage());
        self::assertFalse($userMessage->isRead());
        $userMessage->markRead();
        self::assertTrue($userMessage->isRead());
    }
}
