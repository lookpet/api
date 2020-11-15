<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\User;

use App\Controller\User\UserMessageController;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\User;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\Repository\PetLikeRepositoryInterface;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserMessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Fixture\UserFixture;
use Tests\Unit\Traits\CreateContainerTrait;

/**
 * @group unit
 * @covers \App\Controller\User\UserMessageController
 */
final class UserMessageControllerTest extends TestCase
{
    use CreateContainerTrait;

    private const USER_FOLLOWER_ID = 'user-follower-id';
    private const SLUG = 'super-slug';
    private const PET_TYPE = 'dog';
    private const USER_ID = 'user-id';
    private const PET_LIKE_ID = 'pet-like-id';

    private const TO_USER_ID = 'to-user-id';
    private const TO_USER_SLUG = 'to-user-slug';

    private const USER_NOT_EXIST_MESSAGE = 'User not exist';
    private const MESSAGE_CAN_NOT_BE_EMPTY_MESSAGE = 'Message cannot be empty';
    private const MESSAGE = 'Hello!';

    private UserRepositoryInterface $userRepository;
    private UserMessageRepositoryInterface $userMessageRepository;
    private PetLikeRepositoryInterface $petLikeRepository;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;
    private User $user;
    private UserMessageController $userMessageController;

    public function testUserChat(): void
    {
        $this->userMessageController->setContainer(
            $this->createTokenContainer()
        );
        $request = new Request([], [
            'message' => self::MESSAGE,
        ]);
        $userTo = new User(self::TO_USER_ID, self::TO_USER_SLUG);
        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(UserFixture::SLUG)
            ->willReturn($userTo);

        $utm = new Utm();

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log')
            ->with(
                new EventType(EventType::SEND_MESSAGE),
                $this->user,
                $utm,
                EventContext::createByUser($userTo)
            );

        $result = json_decode(
            $this->userMessageController->chat(
                UserFixture::SLUG,
                $request
            )->getContent(),
            true
        );
        $firstResult = array_shift($result);
        self::assertSame(self::MESSAGE, $firstResult['message']);
        self::assertSame(self::TO_USER_SLUG, $firstResult['to']['slug']);
        self::assertSame(UserFixture::SLUG, $firstResult['from']['slug']);
    }

    public function testUserChatFailsBecauseMessageIsEmpty(): void
    {
        $request = new Request([], [
            'message' => '',
        ]);

        $result = json_decode(
            $this->userMessageController->chat(
                UserFixture::SLUG,
                $request
            )->getContent(),
            true
        );
        self::assertSame(self::MESSAGE_CAN_NOT_BE_EMPTY_MESSAGE, $result['message']);
    }

    public function testFollowFailsBecausePetIsNotFound(): void
    {
        $request = new Request([], [
            'message' => self::MESSAGE,
        ]);

        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findBySlug')
            ->with(UserFixture::SLUG)
            ->willReturn(null);

        $result = json_decode(
            $this->userMessageController->chat(
                UserFixture::SLUG,
                $request
            )->getContent(),
            true
        );

        self::assertSame(self::USER_NOT_EXIST_MESSAGE, $result['message']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userMessageRepository = $this->createMock(UserMessageRepositoryInterface::class);
        $this->requestUtmBuilder = $this->createMock(RequestUtmBuilderInterface::class);
        $this->userEventRepository = $this->createMock(UserEventRepositoryInterface::class);
        $this->user = new User(UserFixture::ID, UserFixture::SLUG);
        $this->userMessageController = new UserMessageController(
            $this->userRepository,
            $this->userMessageRepository,
            $this->requestUtmBuilder,
            $this->userEventRepository
        );
    }
}
