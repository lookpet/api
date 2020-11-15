<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\User;

use App\Controller\User\UserFollowController;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\User;
use App\Entity\UserFollower;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\PetDomain\VO\Uuid;
use App\Repository\PetLikeRepositoryInterface;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserFollowerRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Fixture\UserFixture;
use Tests\Unit\Traits\CreateContainerTrait;

/**
 * @group unit
 * @covers \App\Controller\User\UserFollowerController
 */
final class UserFollowerControllerTest extends TestCase
{
    use CreateContainerTrait;

    private const USER_FOLLOWER_ID = 'user-follower-id';
    private const SLUG = 'super-slug';
    private const PET_TYPE = 'dog';
    private const USER_ID = 'user-id';
    private const PET_LIKE_ID = 'pet-like-id';

    private const USER_NOT_EXIST_MESSAGE = 'User not exist';

    private UserRepositoryInterface $userRepository;
    private UserFollowerRepositoryInterface $userFollowerRepository;
    private PetLikeRepositoryInterface $petLikeRepository;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;
    private Request $request;
    private User $user;
    private UserFollowController $userFollowController;

    public function testUserFollows(): void
    {
        $user = new User(UserFixture::ID, UserFixture::SLUG);
        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(UserFixture::SLUG)
            ->willReturn($user);

        $this->userFollowerRepository
            ->expects(self::atLeastOnce())
            ->method('getUserFollower')
            ->with(
                $user,
                $this->user
            )
            ->willReturn(null);

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
                new EventType(EventType::USER_FOLLOW),
                $this->user,
                $utm,
                EventContext::createByUser($user)
            );

        $result = json_decode(
            $this->userFollowController->follow(
                UserFixture::SLUG,
                $this->request
            )->getContent(),
            true
        );
        self::assertTrue($result['hasFollower']);
        self::assertSame(1, $result['total']);
    }

    public function testUserUnFollows(): void
    {
        $user = new User(UserFixture::ID, UserFixture::SLUG);
        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(UserFixture::SLUG)
            ->willReturn($user);

        $this->userFollowerRepository
            ->expects(self::atLeastOnce())
            ->method('getUserFollower')
            ->with(
                $user,
                $this->user
            )
            ->willReturn(new UserFollower(
                new Uuid(self::USER_FOLLOWER_ID),
                $user,
                $this->user
            ));

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
                new EventType(EventType::USER_UNFOLLOW),
                $this->user,
                $utm,
                EventContext::createByUser($user)
            );

        $result = json_decode(
            $this->userFollowController->follow(
                UserFixture::SLUG,
                $this->request
            )->getContent(),
            true
        );

        self::assertFalse($result['hasFollower']);
        self::assertSame(0, $result['total']);
    }

    public function testFollowFailsBecausePetIsNotFound(): void
    {
        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(UserFixture::SLUG)
            ->willReturn(null);

        $result = json_decode(
            $this->userFollowController->follow(
                UserFixture::SLUG,
                $this->request
            )->getContent(),
            true
        );

        self::assertSame(self::USER_NOT_EXIST_MESSAGE, $result['message']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userFollowerRepository = $this->createMock(UserFollowerRepositoryInterface::class);
        $this->requestUtmBuilder = $this->createMock(RequestUtmBuilderInterface::class);
        $this->userEventRepository = $this->createMock(UserEventRepositoryInterface::class);
        $this->request = new Request();
        $this->user = new User(UserFixture::ID);
        $this->userFollowController = new UserFollowController(
            $this->userRepository,
            $this->userFollowerRepository,
            $this->requestUtmBuilder,
            $this->userEventRepository
        );
        $this->userFollowController->setContainer(
            $this->createTokenContainer()
        );
    }
}
