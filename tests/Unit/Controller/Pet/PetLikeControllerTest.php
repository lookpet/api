<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\Pet;

use App\Controller\Pet\PetLikeController;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\Pet;
use App\Entity\PetLike;
use App\Entity\User;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Slug;
use App\PetDomain\VO\Utm;
use App\Repository\PetLikeRepositoryInterface;
use App\Repository\PetRepositoryInterface;
use App\Repository\UserEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Traits\CreateContainerTrait;

/**
 * @group unit
 * @covers \App\Controller\Pet\PetLikeController
 */
class PetLikeControllerTest extends TestCase
{
    use CreateContainerTrait;

    private const SLUG = 'super-slug';
    private const PET_TYPE = 'dog';
    private const USER_ID = 'user-id';
    private const PET_LIKE_ID = 'pet-like-id';

    private const PET_NOT_EXIST_MESSAGE = 'Pet not exist';

    private PetRepositoryInterface $petRepository;
    private PetLikeRepositoryInterface $petLikeRepository;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;
    private Request $request;
    private User $user;
    private PetLikeController $petLikeController;

    public function testItAddsLike(): void
    {
        $this->petLikeController->setContainer(
            $this->createTokenContainer()
        );

        $pet = new Pet(self::PET_TYPE, self::SLUG);
        $this->petRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(new Slug(self::SLUG))
            ->willReturn($pet);

        $this->petLikeRepository
            ->expects(self::atLeastOnce())
            ->method('getUserPetLike')
            ->with(
                $this->user,
                $pet
            )
            ->willReturn(null);

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('persist');

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $utm = new Utm();

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($this->request)
            ->willReturn($utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log')
            ->with(
                new EventType(EventType::PET_LIKE),
                $this->user,
                $utm,
                EventContext::createByPet($pet)
            );

        $result = json_decode(
            $this->petLikeController->like(
                self::SLUG,
                $this->request
            )->getContent(),
            true
        );
        self::assertTrue($result['hasLike']);
        self::assertSame(1, $result['total']);
    }

    public function testItRemovesLike(): void
    {
        $this->petLikeController->setContainer(
            $this->createTokenContainer()
        );

        $pet = new Pet(self::PET_TYPE, self::SLUG);
        $this->petRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(new Slug(self::SLUG))
            ->willReturn($pet);

        $this->petLikeRepository
            ->expects(self::atLeastOnce())
            ->method('getUserPetLike')
            ->with(
                $this->user,
                $pet
            )
            ->willReturn(new PetLike(
                $pet,
                $this->user,
                self::PET_LIKE_ID
            ));

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('persist');

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $utm = new Utm();

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($this->request)
            ->willReturn($utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log')
            ->with(
                new EventType(EventType::PET_UNLIKE),
                $this->user,
                $utm,
                EventContext::createByPet($pet)
            );

        $result = json_decode(
            $this->petLikeController->like(
                self::SLUG,
                $this->request
            )->getContent(),
            true
        );

        self::assertFalse($result['hasLike']);
        self::assertSame(0, $result['total']);
    }

    public function testLikeFailsBecausePetIsNotFound(): void
    {
        $this->petRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(new Slug(self::SLUG))
            ->willReturn(null);

        $result = $this->petLikeController->like(
            self::SLUG,
            $this->request
        );
        $result = json_decode($result->getContent(), true);

        self::assertSame(self::PET_NOT_EXIST_MESSAGE, $result['message']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->petRepository = $this->createMock(PetRepositoryInterface::class);
        $this->petLikeRepository = $this->createMock(PetLikeRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->requestUtmBuilder = $this->createMock(RequestUtmBuilderInterface::class);
        $this->userEventRepository = $this->createMock(UserEventRepositoryInterface::class);
        $this->request = new Request();
        $this->user = new User(self::USER_ID);
        $this->petLikeController = new PetLikeController(
            $this->petRepository,
            $this->petLikeRepository,
            $this->entityManager,
            $this->requestUtmBuilder,
            $this->userEventRepository
        );
    }
}
