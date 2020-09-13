<?php

namespace App\Service;

use App\EmailTemplates\EmailTemplateDtoInterface;

interface EmailTemplateSenderInterface
{
    public function send(EmailTemplateDtoInterface $emailTemplateDto): void;
}
