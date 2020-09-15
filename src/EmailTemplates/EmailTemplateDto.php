<?php

namespace App\EmailTemplates;

use App\PetDomain\VO\EmailRecipient;

class EmailTemplateDto implements EmailTemplateDtoInterface
{
    /**
     * @var EmailRecipient
     */
    private ?EmailRecipient $from;
    /**
     * @var EmailRecipient
     */
    private EmailRecipient $to;
    private string $subject;
    private int $templateId;
    private bool $isTemplateLanguage;
    private array $variables = [];

    public function __construct(EmailRecipient $to, string $subject, int $templateId, bool $isTemplateLanguage = true)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->templateId = $templateId;
        $this->isTemplateLanguage = $isTemplateLanguage;
        $this->from = null;
    }

    public function setFrom(?EmailRecipient $from): void
    {
        $this->from = $from;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function hasVariables(): bool
    {
        return count($this->variables) > 0;
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
    public function getFrom(): ?EmailRecipient
    {
        return $this->from;
    }

    public function hasFrom(): bool
    {
        return $this->getFrom() !== null;
    }
}
