<?php

namespace Tests\Unit\Service;

use App\EmailTemplates\EmailTemplateDto;
use App\Entity\User;
use App\PetDomain\VO\EmailRecipient;
use App\Service\EmailTemplateSenderInterface;
use App\Service\Notification\WelcomeEmailNotifier;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \App\Service\Notification\WelcomeEmailNotifier
 * @group unit
 */
class WelcomeEmailNotifierTest extends TestCase
{
    private const USER_ID = 'some-id';
    private const FIRST_NAME = 'Bob';
    private const EMAIL = 'test@some.com';
    private const EMAIL_LOOKPET = 'test@look.pet';
    private const EMAIL_SUBJECT = 'Добро пожаловать на look.pet';
    private const MJ_TEMPLATE_WELCOME = 1685295;
    private EmailTemplateSenderInterface $emailTemplateSender;
    private WelcomeEmailNotifier $welcomeNotifier;
    private TranslatorInterface $translator;

    public function testItNotify(): void
    {
        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setFirstName(self::FIRST_NAME);
        $this->translator
            ->expects(self::atLeastOnce())
            ->method('trans')
            ->with('EMAIL_WELCOME_SUBJECT')
            ->willReturn(self::EMAIL_SUBJECT);
        $this->emailTemplateSender
            ->expects(self::once())
            ->method('send')
            ->with(
                new EmailTemplateDto(
                    EmailRecipient::create(
                        self::EMAIL,
                        self::FIRST_NAME
                    ),
                    self::EMAIL_SUBJECT,
                    self::MJ_TEMPLATE_WELCOME
                )
            );

        $this->welcomeNotifier->notify($user);
    }

    public function testItWillNotNotifyBecauseUserIsLookPet(): void
    {
        $user = new User();
        $user->setEmail(self::EMAIL_LOOKPET);
        $user->setFirstName(self::FIRST_NAME);
        $this->emailTemplateSender
            ->expects(self::never())
            ->method('send');

        $this->welcomeNotifier->notify($user);
    }

    public function testItWillNotNotifyBecauseEmailIsNull(): void
    {
        $user = new User();
        $user->setEmail(null);
        $user->setFirstName(self::FIRST_NAME);
        $this->emailTemplateSender
            ->expects(self::never())
            ->method('send');

        $this->welcomeNotifier->notify($user);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->emailTemplateSender = $this->createMock(EmailTemplateSenderInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->welcomeNotifier = new WelcomeEmailNotifier(
            $this->emailTemplateSender,
            $this->translator
        );
    }
}
