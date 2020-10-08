<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Dto\Pet\PetDto;
use App\Entity\Media;
use App\Entity\Pet;
use App\Entity\PetComment;
use App\Entity\PetLike;
use App\Entity\User;
use App\PetDomain\VO\Age;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Fixture\PetFixture;

/**
 * @group unit
 */
final class PetTest extends TestCase
{
    private const TYPE = 'cat';
    private const SLUG = 'new-slug';
    private const NAME = 'Leo';

    private const USER_ID = 'user-id';

    public function testGettersSetters(): void
    {
        $user = new User(null, null, self::USER_ID);
        $pet = new Pet(PetFixture::TYPE, PetFixture::SLUG, PetFixture::ID, PetFixture::NAME, $user);
        self::assertSame(PetFixture::TYPE, $pet->getType());
        self::assertSame(PetFixture::SLUG, $pet->getSlug());
        self::assertSame(PetFixture::NAME, $pet->getName());
        self::assertSame(PetFixture::ID, $pet->getId());
        self::assertSame($user, $pet->getUser());

        self::assertFalse($pet->isDeleted());
        $pet->delete();
        self::assertTrue($pet->isDeleted());

        $pet->setUser(null);
        self::assertNull($pet->getUser());

        self::assertEmpty($pet->getMedia());
        $media = $this->createMock(Media::class);
        $pet->addMedia($media);
        self::assertSame($media, $pet->getMedia()->first());
        $pet->removeMedia($media);
        self::assertEmpty($pet->getMedia());

        self::assertEmpty($pet->getComments());
        $comment = $this->createMock(PetComment::class);
        $pet->addComments($comment);
        self::assertSame($comment, $pet->getComments()->first());
        $pet->removeComments($comment);
        self::assertEmpty($pet->getComments());

        self::assertEmpty($pet->getLikes());
        $like = new PetLike($pet, $user);
        $pet->addLikes($like);
        self::assertSame($like, $pet->getLikes()->first());
        self::assertTrue($pet->hasLike($user));
        $pet->removeLike($like);
        self::assertEmpty($pet->getLikes());
    }

    public function testItCreatesPetFromDto(): void
    {
        $user = $this->createMock(User::class);
        $dateOfBirth = new \DateTimeImmutable('-1 day');
        $age = new Age($dateOfBirth);

        $petDto = new PetDto();
        $petDto->setType(PetFixture::TYPE);
        $petDto->setSlug(PetFixture::SLUG);
        $petDto->setName(PetFixture::NAME);
        $petDto->setId(PetFixture::ID);
        $petDto->setCity(PetFixture::CITY);
        $petDto->setPlaceId(PetFixture::PLACE_ID);
        $petDto->setBreed(PetFixture::BREED);
        $petDto->setPrice(PetFixture::PRICE);
        $petDto->setFatherName(PetFixture::FATHER_NAME);
        $petDto->setMotherName(PetFixture::MOTHER_NAME);
        $petDto->setColor(PetFixture::COLOR);
        $petDto->setAbout(PetFixture::ABOUT);
        $petDto->setEyeColor(PetFixture::EYE_COLOR);
        $petDto->setDateOfBirth($dateOfBirth);
        $petDto->setIsAlive(false);
        $petDto->setIsLookingForOwner(true);
        $petDto->setIsFree(true);
        $petDto->setIsSold(false);

        $pet = new Pet(PetFixture::TYPE, PetFixture::SLUG, PetFixture::ID, PetFixture::NAME, $user);

        $pet->updateFromDto($petDto, $user);

        self::assertSame($user, $pet->getUser());
        self::assertSame(PetFixture::TYPE, $pet->getType());
        self::assertSame(PetFixture::SLUG, $pet->getSlug());
        self::assertSame(PetFixture::NAME, $pet->getName());
        self::assertSame(PetFixture::ID, $pet->getId());
        self::assertSame(PetFixture::CITY, $pet->getCity());
        self::assertSame(PetFixture::PLACE_ID, $pet->getPlaceId());
        self::assertSame(PetFixture::BREED, $pet->getBreed());
        self::assertSame(PetFixture::PRICE, $pet->getPrice());
        self::assertSame(PetFixture::FATHER_NAME, $pet->getFatherName());
        self::assertSame(PetFixture::MOTHER_NAME, $pet->getMotherName());
        self::assertSame(PetFixture::COLOR, $pet->getColor());
        self::assertSame(PetFixture::EYE_COLOR, $pet->getEyeColor());
        self::assertSame(PetFixture::ABOUT, $pet->getAbout());
        self::assertSame($dateOfBirth, $pet->getDateOfBirth());
        self::assertTrue($pet->isLookingForOwner());
        self::assertFalse($pet->isAlive());
        self::assertFalse($pet->isSold());
        self::assertTrue($pet->isFree());
        self::assertTrue($age->equals($pet->getAge()));
    }

    public function testItThrowsExceptionWhenSlugIsNotSet(): void
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('Slug cannot be empty');
        new Pet(PetFixture::TYPE, null);
    }
}
