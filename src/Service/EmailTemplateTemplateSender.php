<?php

namespace App\Service;

use App\EmailTemplates\EmailTemplateDtoInterface;
use Mailjet\Client;
use Mailjet\Resources;

class EmailTemplateTemplateSender implements EmailTemplateSenderInterface
{
    /**
     * @var Client
     */
    private Client $emailSenderClient;

    public function __construct(Client $emailSenderClient)
    {
        $this->emailSenderClient = $emailSenderClient;
    }

    public function send(EmailTemplateDtoInterface $templateDto): void
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $templateDto->getFrom()->email(),
                        'Name' => $templateDto->getFrom()->name(),
                    ],
                    'To' => [
                        [
                            'Email' => $templateDto->getTo()->email(),
                            'Name' => $templateDto->getTo()->name(),
                        ],
                    ],
                    'TemplateID' => $templateDto->getTemplateId(),
                    'TemplateLanguage' => $templateDto->isTemplateLanguage(),
                    'Subject' => $templateDto->getSubject(),
                ],
            ],
        ];
        $this->emailSenderClient->post(
          Resources::$Email,
          [
              'body' => $body,
          ]);
    }
}
