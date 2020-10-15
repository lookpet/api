<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\Authentication;

use App\Controller\Authentication\AuthenticationController;
use App\Dto\Authentication\UserLoginDtoBuilder;
use App\EmailTemplates\EmailTemplateDto;
use App\Entity\ApiToken;
use App\Entity\User;
use App\PetDomain\VO\EmailRecipient;
use App\Repository\UserRepositoryInterface;
use App\Service\EmailTemplateSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    private ValidatorInterface $validator;
    private UserRepositoryInterface $userRepository;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private EntityManagerInterface $entityManager;
    private EmailTemplateSenderInterface $emailTemplateSender;

    private User $user;

    private AuthenticationController $authenticationController;

    public function testLoginPositive(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

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
            2
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

        $emailTemplateDto = new EmailTemplateDto(
            EmailRecipient::create(
                UserFixture::EMAIL,
                UserFixture::FIRST_NAME
            ),
            'Добро пожаловать на look.pet',
            1685295
        );

        $this->emailTemplateSender
            ->expects(self::once())
            ->method('send')
            ->with($emailTemplateDto);

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('persist');

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('flush');

        $result = $this->authenticationController->register(
            $request,
            $this->emailTemplateSender
        );

        $decodedResponse = json_decode($result->getContent());
        $expiresAt = new \DateTimeImmutable('+7 days', new \DateTimeZone('Europe/London'));
        self::assertNotEmpty($decodedResponse->token);
        self::assertEqualsWithDelta(
            $expiresAt->getTimestamp(),
            (new \DateTimeImmutable($decodedResponse->expires_at->date))->getTimestamp(),
            2
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

        $this->positiveDtoValidation();

        $this->userRepository
            ->expects(self::once())
            ->method('findByEmail')
            ->with(UserFixture::EMAIL)
            ->willReturn($this->user);

        $result = $this->authenticationController->register(
            $request,
            $this->emailTemplateSender
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
        $this->emailTemplateSender = $this->createMock(EmailTemplateSenderInterface::class);
        $this->authenticationController = new AuthenticationController(
            $this->userRepository,
            $this->validator,
            $this->userPasswordEncoder,
            $this->entityManager,
            new UserLoginDtoBuilder(
                $this->validator
            )
        );
        $this->authenticationController->setContainer(
            $this->createMock(ContainerInterface::class)
        );
    }

    private function positiveDtoValidation(): void
    {
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email';

        $this->validator
            ->expects(self::exactly(2))
            ->method('validate')
            ->withConsecutive(
                [
                    UserFixture::EMAIL,
                    $emailConstraint,
                ],
                [
                    UserFixture::PASSWORD,
                    new Assert\Length([
                        'min' => 6,
                    ]),
                ]
            )
            ->willReturnOnConsecutiveCalls([], []);
    }
}
