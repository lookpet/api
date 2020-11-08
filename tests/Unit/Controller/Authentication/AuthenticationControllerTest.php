<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\Authentication;

use App\Controller\Authentication\AuthenticationController;
use App\Dto\Authentication\UserLoginDto;
use App\Dto\Authentication\UserLoginDtoBuilder;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Message\MailWelcomeMessage;
use App\PetDomain\VO\Utm;
use App\PetDomain\VO\Uuid;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Unit\Fixture\UserFixture;

/**
 * @group unit
 * @covers \App\Controller\Authentication\AuthenticationController
 */
final class AuthenticationControllerTest extends TestCase
{
    private const INVALID_EMAIL_OR_PASSWORD_MESSAGE = 'Invalid email or password';
    private const USER_ALREADY_EXIST_MESSAGE = 'User already exist';

    private const EMAIL = 'test@mail.com';
    private const NAME = 'Snappy';

    private ValidatorInterface $validator;
    private UserRepositoryInterface $userRepository;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private EntityManagerInterface $entityManager;
    private UserLoginDtoBuilder $userLoginDtoBuilder;
    private MessageBusInterface $messageBus;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    private User $user;

    private AuthenticationController $authenticationController;

    public function testLoginPositive(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password')
        );

        $this->userLoginDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($userLoginDto);

        $this->positiveDtoValidation();

        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findByEmail')
        ->with(UserFixture::EMAIL)
        ->willReturn($user);

        $this->userPasswordEncoder
            ->expects(self::once())
            ->method('isPasswordValid')
        ->with($user, UserFixture::PASSWORD)
        ->willReturn(true);

        $apiToken = new ApiToken($user);

        $user->expects(self::atLeastOnce())
            ->method('getActiveApiToken')
            ->willReturn($apiToken);

        $user->expects(self::atLeastOnce())
            ->method('hasActiveApiToken')
            ->willReturn(false);

        $this->entityManager
            ->expects(self::atLeastOnce())
        ->method('persist');

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('flush');

        $utm = new Utm();

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log');

        $result = $this->authenticationController->login(
            $request
        );
        self::assertSame(Response::HTTP_OK, $result->getStatusCode());
        $decodedResponse = json_decode($result->getContent());
        $expiresAt = new \DateTimeImmutable('+7 days', new \DateTimeZone('Europe/London'));
        self::assertSame($apiToken->getToken(), $decodedResponse->token);
        self::assertEqualsWithDelta(
            $expiresAt->getTimestamp(),
            (new \DateTimeImmutable($decodedResponse->expires_at->date))->getTimestamp(),
            3600
        );
        self::assertSame(3, $decodedResponse->expires_at->timezone_type);
    }

    public function testLoginFailsBecauseUserIsNotFound(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password')
        );

        $this->userLoginDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($userLoginDto);

        $this->positiveDtoValidation();

        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn(null);

        $result = $this->authenticationController->login(
            $request
        );
        self::assertSame(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $decodedResponse = json_decode($result->getContent());
        self::assertSame(self::INVALID_EMAIL_OR_PASSWORD_MESSAGE, $decodedResponse->message);
    }

    public function testLoginFailsBecausePasswordIsNotValid(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password')
        );

        $this->userLoginDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($userLoginDto);

        $this->positiveDtoValidation();

        $this->userRepository
            ->expects(self::atLeastOnce())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn(
                $this->user
            );

        $this->userPasswordEncoder
            ->expects(self::once())
            ->method('isPasswordValid')
            ->with($this->user, UserFixture::PASSWORD)
            ->willReturn(false);

        $result = $this->authenticationController->login(
            $request
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $decodedResponse = json_decode($result->getContent());
        self::assertSame(self::INVALID_EMAIL_OR_PASSWORD_MESSAGE, $decodedResponse->message);
    }

    public function testRegisterPositive(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password'),
            $request->request->get('firstName')
        );

        $this->userLoginDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($userLoginDto);

        $this->positiveDtoValidation();

        $this->userRepository
            ->expects(self::once())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn(null);

        $this->entityManager
            ->expects(self::exactly(2))
            ->method('persist');

        $this->entityManager->expects(self::once())->method('flush');

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('persist');

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('flush');

        $this->userRepository
            ->expects(self::once())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn($this->user);

        $this->user
            ->expects(self::atLeastOnce())
            ->method('getUuid')
            ->willReturn(new Uuid(UserFixture::ID));

        $this->messageBus->expects(self::exactly(1))
            ->method('dispatch')
            ->withConsecutive(
                ...[self::isInstanceOf(MailWelcomeMessage::class)]
            )
            ->willReturn(new Envelope(new MailWelcomeMessage(new Uuid(UserFixture::ID))));
        $this->messageBus
            ->expects(self::atLeastOnce())
            ->method('dispatch');

        $utm = new Utm();

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log');

        $result = $this->authenticationController->register(
            $request
        );

        $decodedResponse = json_decode($result->getContent());
        var_dump($decodedResponse);
        $expiresAt = new \DateTimeImmutable('+7 days', new \DateTimeZone('Europe/London'));
        self::assertNotEmpty($decodedResponse->token);
        self::assertEqualsWithDelta(
            $expiresAt->getTimestamp(),
            (new \DateTimeImmutable($decodedResponse->expires_at->date))->getTimestamp(),
            3600
        );
        self::assertSame(3, $decodedResponse->expires_at->timezone_type);
    }

    public function testRegistrationFailsWhenUserAlreadyExist(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $userLoginDto = new UserLoginDto(
            $request->request->get('email'),
            $request->request->get('password')
        );

        $this->userLoginDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request)
            ->willReturn($userLoginDto);

        $this->positiveDtoValidation();

        $this->userRepository
            ->expects(self::once())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn($this->user);

        $result = $this->authenticationController->register(
            $request
        );

        $decodedResponse = json_decode($result->getContent());
        self::assertSame(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        self::assertSame(self::USER_ALREADY_EXIST_MESSAGE, $decodedResponse->message);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userPasswordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->user = $this->createMock(User::class);
        $this->userLoginDtoBuilder = $this->createMock(UserLoginDtoBuilder::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->requestUtmBuilder = $this->createMock(RequestUtmBuilderInterface::class);
        $this->userEventRepository = $this->createMock(UserEventRepositoryInterface::class);
        $this->authenticationController = new AuthenticationController(
            $this->userRepository,
            $this->validator,
            $this->userPasswordEncoder,
            $this->entityManager,
            $this->userLoginDtoBuilder,
            $this->messageBus,
            $this->requestUtmBuilder,
            $this->userEventRepository
        );
        $this->authenticationController->setContainer(
            $this->createMock(ContainerInterface::class)
        );
    }

    private function positiveDtoValidation(): void
    {
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email';
    }
}
