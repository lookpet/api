<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Pet;

use App\Dto\Pet\PetDtoBuilder;
use App\Entity\Media;
use App\Repository\MediaRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Fixture\PetFixture;

/**
 * @group unit
 */
class PetDtoBuilderTest extends TestCase
{
    private const REQUEST_MEDIA = ['some-media-id'];
    private const EMPTY_TYPE_EXCEPTION_MESSAGE = 'Empty type';
    private MediaRepositoryInterface $mediaRepository;
    private PetDtoBuilder $petDtoBuilder;

    public function testItBuildsDto(): void
    {
        $media = $this->createMock(Media::class);
        $request = new Request([], [
            'type' => PetFixture::TYPE,
            'slug' => PetFixture::SLUG,
            'name' => PetFixture::NAME,
            'breed' => PetFixture::BREED,
            'color' => PetFixture::COLOR,
            'eyeColor' => PetFixture::EYE_COLOR,
            'gender' => PetFixture::GENDER,
            'about' => PetFixture::ABOUT,
            'fatherName' => PetFixture::FATHER_NAME,
            'motherName' => PetFixture::MOTHER_NAME,
            'city' => PetFixture::CITY,
            'placeId' => PetFixture::PLACE_ID,
            'price' => PetFixture::PRICE,
            'isLookingForNewOwner' => true,
            'isFree' => true,
            'isSold' => true,
            'media' => self::REQUEST_MEDIA,
        ]);

        $this->mediaRepository
            ->expects(self::atLeastOnce())
            ->method('findById')
            ->with(self::REQUEST_MEDIA[0])
            ->willReturn($media);

        $petDto = $this->petDtoBuilder->build($request, PetFixture::ID);
        self::assertSame(PetFixture::TYPE, $petDto->getType());
        self::assertSame(PetFixture::ID, $petDto->getId());
        self::assertSame(PetFixture::SLUG, $petDto->getSlug());
        self::assertSame(PetFixture::NAME, $petDto->getName());
        self::assertSame(PetFixture::BREED, $petDto->getBreed());
        self::assertSame(PetFixture::COLOR, $petDto->getColor());
        self::assertSame(PetFixture::EYE_COLOR, $petDto->getEyeColor());
        self::assertSame(PetFixture::GENDER, $petDto->getGender());
        self::assertSame(PetFixture::ABOUT, $petDto->getAbout());
        self::assertSame(PetFixture::FATHER_NAME, $petDto->getFatherName());
        self::assertSame(PetFixture::MOTHER_NAME, $petDto->getMotherName());
        self::assertSame(PetFixture::CITY, $petDto->getCity());
        self::assertSame(PetFixture::PLACE_ID, $petDto->getPlaceId());
        self::assertSame(PetFixture::PRICE, $petDto->getPrice());
        self::assertTrue($petDto->isLookingForNewOwner());
        self::assertTrue($petDto->isSold());
        self::assertTrue($petDto->isFree());
        self::assertSame([$media], $petDto->getMedia());
    }

    public function testItThrowsEmptyTypeException(): void
    {
        $request = new Request();
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(self::EMPTY_TYPE_EXCEPTION_MESSAGE);
        $this->petDtoBuilder->build($request);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $this->petDtoBuilder = new PetDtoBuilder($this->mediaRepository);
    }
}
