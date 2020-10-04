<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Pet;

use App\Dto\Pet\PetDto;
use App\Entity\Media;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Fixture\PetFixture;

/**
 * @group unit
 */
class PetDtoTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $petDto = new PetDto();
        $dateOfBirth = new \DateTime();

        $petDto->setId(PetFixture::ID);
        self::assertSame(PetFixture::ID, $petDto->getId());

        $petDto->setType(PetFixture::TYPE);
        self::assertSame(PetFixture::TYPE, $petDto->getType());

        $petDto->setSlug(PetFixture::SLUG);
        self::assertSame(PetFixture::SLUG, $petDto->getSlug());

        $petDto->setName(PetFixture::NAME);
        self::assertSame(PetFixture::NAME, $petDto->getName());

        $petDto->setBreed(PetFixture::BREED);
        self::assertSame(PetFixture::BREED, $petDto->getBreed());

        $petDto->setColor(PetFixture::COLOR);
        self::assertSame(PetFixture::COLOR, $petDto->getColor());

        $petDto->setEyeColor(PetFixture::EYE_COLOR);
        self::assertSame(PetFixture::EYE_COLOR, $petDto->getEyeColor());

        $petDto->setDateOfBirth($dateOfBirth);
        self::assertSame($dateOfBirth, $petDto->getDateOfBirth());

        $petDto->setGender(PetFixture::GENDER);
        self::assertSame(PetFixture::GENDER, $petDto->getGender());

        $petDto->setAbout(PetFixture::ABOUT);
        self::assertSame(PetFixture::ABOUT, $petDto->getAbout());

        self::assertFalse($petDto->isLookingForNewOwner());
        $petDto->setIsLookingForOwner(true);
        self::assertTrue($petDto->isLookingForNewOwner());

        $petDto->setFatherName(PetFixture::FATHER_NAME);
        self::assertSame(PetFixture::FATHER_NAME, $petDto->getFatherName());

        $petDto->setMotherName(PetFixture::MOTHER_NAME);
        self::assertSame(PetFixture::MOTHER_NAME, $petDto->getMotherName());

        $petDto->setCity(PetFixture::CITY);
        self::assertSame(PetFixture::CITY, $petDto->getCity());

        $petDto->setPlaceId(PetFixture::PLACE_ID);
        self::assertSame(PetFixture::PLACE_ID, $petDto->getPlaceId());

        $petDto->setPrice(PetFixture::PRICE);
        self::assertSame(PetFixture::PRICE, $petDto->getPrice());

        self::assertFalse($petDto->isFree());
        $petDto->setIsFree(true);
        self::assertTrue($petDto->isFree());

        self::assertFalse($petDto->isSold());
        $petDto->setIsSold(true);
        self::assertTrue($petDto->isSold());

        $media = $this->createMock(Media::class);
        self::assertEmpty($petDto->getMedia());
        $petDto->setMedia($media);
        self::assertSame([$media], $petDto->getMedia());
    }
}
