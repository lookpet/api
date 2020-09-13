<?php

namespace App\EmailTemplates;

use App\PetDomain\VO\EmailRecipient;

interface EmailTemplateDtoInterface
{
    /**
     * @return bool
     */
    public function isTemplateLanguage(): bool;

    /**
     * @return int
     */
    public function getTemplateId(): int;

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return EmailRecipient
     */
    public function getTo(): EmailRecipient;

    /**
     * @return EmailRecipient
     */
    public function getFrom(): EmailRecipient;
}
