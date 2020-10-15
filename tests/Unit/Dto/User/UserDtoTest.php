<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\User;

use App\Dto\User\UserDto;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Fixture\UserFixture;

/**
 * @group unit
 * @covers \App\Dto\User\UserDto
 */
final class UserDtoTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $userDto = new UserDto();
        $dateOfBirth = new \DateTimeImmutable();

        $userDto->setSlug(UserFixture::SLUG);
        self::assertSame(UserFixture::SLUG, $userDto->getSlug());
        $userDto->setFirstName(UserFixture::FIRST_NAME);
        self::assertSame(UserFixture::FIRST_NAME, $userDto->getFirstName());
        $userDto->setLastName(UserFixture::LAST_NAME);
        self::assertSame(UserFixture::LAST_NAME, $userDto->getLastName());
        $userDto->setCity(UserFixture::CITY);
        self::assertSame(UserFixture::CITY, $userDto->getCity());
        $userDto->setPlaceId(UserFixture::PLACE_ID);
        self::assertSame(UserFixture::PLACE_ID, $userDto->getPlaceId());
        $userDto->setPhone(UserFixture::PHONE);
        self::assertSame(UserFixture::PHONE, $userDto->getPhone());
        $userDto->setDateOfBirth($dateOfBirth);
        self::assertSame($dateOfBirth, $userDto->getDateOfBirth());
    }
}
