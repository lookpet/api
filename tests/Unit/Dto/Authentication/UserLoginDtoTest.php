<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Authentication;

use App\Dto\Authentication\UserLoginDto;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Fixture\UserFixture;

/**
 * @group unit
 * @covers \App\Dto\Authentication\UserLoginDto
 */
final class UserLoginDtoTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $userDto = new UserLoginDto(
            UserFixture::EMAIL,
            UserFixture::PASSWORD
        );
        $userDto->setId(UserFixture::ID);
        $userDto->setFirstName(UserFixture::FIRST_NAME);
        $userDto->setSlug(UserFixture::SLUG);

        self::assertSame(UserFixture::EMAIL, $userDto->getEmail());
        self::assertSame(UserFixture::PASSWORD, $userDto->getPassword());
        self::assertSame(UserFixture::ID, $userDto->getId());
        self::assertSame(UserFixture::FIRST_NAME, $userDto->getFirstName());
        self::assertSame(UserFixture::SLUG, $userDto->getSlug());
    }
}
