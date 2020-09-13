<?php

namespace App\EmailTemplates;

use App\PetDomain\VO\EmailRecipient;

class EmailTemplateDto implements EmailTemplateDtoInterface
{
    /**
     * @var EmailRecipient
     */
    private EmailRecipient $from;
    /**
     * @var EmailRecipient
     */
    private EmailRecipient $to;
    private string $subject;
    private int $templateId;
    private bool $isTemplateLanguage;

    public function __construct(EmailRecipient $from, EmailRecipient $to, string $subject, int $templateId, bool $isTemplateLanguage = true)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->templateId = $templateId;
        $this->isTemplateLanguage = $isTemplateLanguage;
    }

    /**
     * @return bool
     */
    public function isTemplateLanguage(): bool
    {
        return $this->isTemplateLanguage;
    }

    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return EmailRecipient
     */
    public function getTo(): EmailRecipient
    {
        return $this->to;
    }

    /**
     * @return EmailRecipient
     */
    public function getFrom(): EmailRecipient
    {
        return $this->from;
    }
}
