<?php

namespace App\Service\Notification;

use App\EmailTemplates\EmailTemplateDto;
use App\Entity\User;
use App\PetDomain\VO\EmailRecipient;
use App\Service\EmailTemplateSenderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class WelcomeEmailNotifier implements EmailNotifyInterface
{
    private EmailTemplateSenderInterface $emailTemplateSender;
    private TranslatorInterface $translator;

    public function __construct(
        EmailTemplateSenderInterface $emailTemplateSender,
        TranslatorInterface $translator
    ) {
        $this->emailTemplateSender = $emailTemplateSender;
        $this->translator = $translator;
    }

    public function notify(User $user): void
    {
        if ($user->allowSendEmailNotifications()) {
            $this->emailTemplateSender->send(
                new EmailTemplateDto(
                    EmailRecipient::create(
                    $user->getEmail(),
                    $user->getFirstName()
                ),
                $this->translator->trans('EMAIL_WELCOME_SUBJECT'),
                (int) $_ENV['MJ_TEMPLATE_WELCOME']
            ));
        }
    }
}
