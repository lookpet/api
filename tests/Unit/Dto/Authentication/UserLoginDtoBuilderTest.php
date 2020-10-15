<?php

namespace Tests\Unit\Dto\Authentication;

use App\Dto\Authentication\UserLoginDtoBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Unit\Fixture\UserFixture;

/**
 * @group unit
 * @covers \App\Dto\Authentication\UserLoginDtoBuilder
 */
final class UserLoginDtoBuilderTest extends TestCase
{
    private const WRONG_EMAIL = 'wrong-email';
    private const SHORT_PASSWORD = '12345';

    private const EMPTY_EMAIL_MESSAGE = 'Empty email';
    private const EMPTY_PASSWORD_MESSAGE = 'Empty password';
    private const INVALID_EMAIL_MESSAGE = 'Invalid email';
    private const PASSWORD_TOO_SHORT_MESSAGE = 'Password too short min length is 6';

    private ValidatorInterface $validator;
    private UserLoginDtoBuilder $userLoginDtoBuilder;

    public function testItBuildsLoginDto(): void
    {
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

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

        $userDto = $this->userLoginDtoBuilder->build($request);
        self::assertSame(UserFixture::EMAIL, $userDto->getEmail());
        self::assertSame(UserFixture::PASSWORD, $userDto->getPassword());
        self::assertSame(UserFixture::FIRST_NAME, $userDto->getFirstName());
        self::assertStringContainsString(mb_strtolower(UserFixture::FIRST_NAME), $userDto->getSlug());
    }

    /**
     * @dataProvider dataTestItThrowsException
     *
     * @param Request $request
     * @param string $exceptionMessage
     */
    public function testUserDtoBuildThrowsException(Request $request, string $exceptionMessage): void
    {
        $this->expectExceptionMessage($exceptionMessage);
        $this->userLoginDtoBuilder->build($request);
    }

    public function dataTestItThrowsException(): array
    {
        return [
            [
                new Request([], [
                    'email' => null,
                    'password' => UserFixture::PASSWORD,
                    'firstName' => UserFixture::FIRST_NAME,
                ]),
                self::EMPTY_EMAIL_MESSAGE,
            ],
            [
                new Request([], [
                    'password' => UserFixture::PASSWORD,
                    'firstName' => UserFixture::FIRST_NAME,
                ]),
                self::EMPTY_EMAIL_MESSAGE,
            ],
            [
                new Request([], [
                    'email' => UserFixture::EMAIL,
                    'firstName' => UserFixture::FIRST_NAME,
                ]),
                self::EMPTY_PASSWORD_MESSAGE,
            ],
            [
                new Request([], [
                    'password' => null,
                    'email' => UserFixture::EMAIL,
                    'firstName' => UserFixture::FIRST_NAME,
                ]),
                self::EMPTY_PASSWORD_MESSAGE,
            ],
        ];
    }

    public function testItFailsBecauseEmailIsInvalid(): void
    {
        $this->expectExceptionMessage(self::INVALID_EMAIL_MESSAGE);
        $request = new Request([], [
            'email' => self::WRONG_EMAIL,
            'password' => UserFixture::PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email';

        $this->validator
            ->expects(self::atLeastOnce())
            ->method('validate')
            ->with(
                self::WRONG_EMAIL,
                $emailConstraint
            )
            ->willReturn([
                'error',
            ]);

        $this->userLoginDtoBuilder->build($request);
    }

    public function testItFailsBecausePasswordIsShort(): void
    {
        $this->expectExceptionMessage(self::PASSWORD_TOO_SHORT_MESSAGE);
        $request = new Request([], [
            'email' => UserFixture::EMAIL,
            'password' => self::SHORT_PASSWORD,
            'firstName' => UserFixture::FIRST_NAME,
        ]);

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
                    self::SHORT_PASSWORD,
                    new Assert\Length([
                        'min' => 6,
                    ]),
                ]
            )
            ->willReturnOnConsecutiveCalls([], ['error']);

        $this->userLoginDtoBuilder->build($request);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->userLoginDtoBuilder = new UserLoginDtoBuilder(
            $this->validator
        );
    }
}
