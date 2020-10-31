<?php

namespace App\Service\Notification;

use App\EmailTemplates\EmailTemplateDto;
use App\Entity\User;
use App\PetDomain\VO\EmailRecipient;
use App\Service\EmailTemplateSenderInterface;

class WelcomeEmailNotifier implements EmailNotifyInterface
{
    private EmailTemplateSenderInterface $emailTemplateSender;

    public function __construct(EmailTemplateSenderInterface $emailTemplateSender)
    {
        $this->emailTemplateSender = $emailTemplateSender;
    }

    public function notify(User $user): void
    {
        if ($user->hasEmail() && !$user->isLookPetUser() && filter_var(getenv('IS_SEND_EMAIL_NOTIFICATIONS'), FILTER_VALIDATE_BOOLEAN) === true) {
            $this->emailTemplateSender->send(new EmailTemplateDto(
                EmailRecipient::create(
                    $user->getEmail(),
                    $user->getFirstName()
                ),
                'Добро пожаловать на look.pet',
                (int) $_ENV['MJ_TEMPLATE_WELCOME']
            ));
        }
    }
}
