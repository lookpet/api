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
        $dateOfBirth = new \DateTimeImmutable('-1 day');
        $age = new Age($dateOfBirth);
        $user = new User(null, null, self::USER_ID);
        $pet = new Pet(PetFixture::TYPE, PetFixture::SLUG, PetFixture::ID, PetFixture::NAME, $user);

        self::assertSame(PetFixture::TYPE, $pet->getType());
        self::assertSame(PetFixture::SLUG, $pet->getSlug());
        self::assertSame(PetFixture::NAME, $pet->getName());
        self::assertSame(PetFixture::ID, $pet->getId());
        self::assertSame($user, $pet->getUser());

        $pet->setSlug(self::SLUG);
        self::assertSame(self::SLUG, $pet->getSlug());

        $pet->setIsAlive(true);
        self::assertTrue($pet->isAlive());

        $pet->setName(self::NAME);
        self::assertSame(self::NAME, $pet->getName());

        $pet->setType(self::TYPE);
        self::assertSame(self::TYPE, $pet->getType());

        $pet->setGender(PetFixture::GENDER);
        self::assertSame(PetFixture::GENDER, $pet->getGender());

        $pet->setBreed(PetFixture::BREED);
        self::assertSame(PetFixture::BREED, $pet->getBreed());

        $pet->setAbout(PetFixture::ABOUT);
        self::assertSame(PetFixture::ABOUT, $pet->getAbout());

        $pet->setIsLookingForOwner(true);
        self::assertTrue($pet->isLookingForOwner());

        $pet->setDateOfBirth($dateOfBirth);
        self::assertSame($dateOfBirth, $pet->getDateOfBirth());

        $pet->setColor(PetFixture::COLOR);
        self::assertSame(PetFixture::COLOR, $pet->getColor());

        $pet->setEyeColor(PetFixture::EYE_COLOR);
        self::assertSame(PetFixture::EYE_COLOR, $pet->getEyeColor());

        $pet->setCity(PetFixture::CITY);
        self::assertSame(PetFixture::CITY, $pet->getCity());

        $pet->setFatherName(PetFixture::FATHER_NAME);
        self::assertSame(PetFixture::FATHER_NAME, $pet->getFatherName());

        $pet->setMotherName(PetFixture::MOTHER_NAME);
        self::assertSame(PetFixture::MOTHER_NAME, $pet->getMotherName());

        $pet->setIsAlive(true);
        self::assertTrue($pet->isAlive());

        $pet->setIsSold(true);
        self::assertTrue($pet->isSold());

        $pet->setIsFree(true);
        self::assertTrue($pet->isFree());

        self::assertFalse($pet->isDeleted());
        $pet->delete();
        self::assertTrue($pet->isDeleted());

        $pet->setUser(null);
        self::assertNull($pet->getUser());

        $pet->setPrice(PetFixture::PRICE);
        self::assertSame(PetFixture::PRICE, $pet->getPrice());

        $pet->setPlaceId(PetFixture::PLACE_ID);
        self::assertSame(PetFixture::PLACE_ID, $pet->getPlaceId());

        self::assertTrue($age->equals($pet->getAge()));

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
        $dateOfBirth = new \DateTimeImmutable();

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
        $petDto->setIsLookingForOwner(true);
        $petDto->setIsFree(true);
        $petDto->setIsSold(false);

        $pet = Pet::createFromDto($petDto, $user);

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
        self::assertFalse($pet->isSold());
        self::assertTrue($pet->isFree());
    }

    public function testItThrowsExceptionWhenSlugIsNotSet(): void
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('Slug cannot be empty');
        new Pet(PetFixture::TYPE, null);
    }
}
