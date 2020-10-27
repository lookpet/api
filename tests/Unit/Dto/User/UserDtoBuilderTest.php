<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\User;

use App\Dto\User\UserDtoBuilder;
use App\Repository\BreederRepositoryInterface;
use App\Repository\MediaRepositoryInterface;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Fixture\UserFixture;

/**
 * @group unit
 * @covers \App\Dto\User\UserDtoBuilder
 */
final class UserDtoBuilderTest extends TestCase
{
    private MediaRepositoryInterface $mediaRepository;
    private UserDtoBuilder $userDtoBuilder;
    private EntityManagerInterface $entityManager;
    private BreederRepositoryInterface $breederRepository;

    public function testItBuildsDto(): void
    {
        $request = new Request([], [
            'firstName' => UserFixture::FIRST_NAME,
            'lastName' => UserFixture::LAST_NAME,
            'phone' => UserFixture::PHONE,
            'description' => UserFixture::DESCRIPTION,
            'city' => UserFixture::CITY,
            'slug' => UserFixture::SLUG,
        ]);

        $userDto = $this->userDtoBuilder->build($request);
        self::assertSame(UserFixture::FIRST_NAME, $userDto->getFirstName());
        self::assertSame(UserFixture::LAST_NAME, $userDto->getLastName());
        self::assertSame(UserFixture::PHONE, $userDto->getPhone());
        self::assertSame(UserFixture::DESCRIPTION, $userDto->getDescription());
        self::assertSame(UserFixture::CITY, $userDto->getCity());
        self::assertSame(UserFixture::SLUG, $userDto->getSlug());
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $this->breederRepository = $this->createMock(BreederRepositoryInterface::class);
        $this->userDtoBuilder = new UserDtoBuilder(
            $this->entityManager,
            $this->breederRepository,
            new Slugify()
        );
    }
}
