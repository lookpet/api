<?php

namespace App\Service;

use App\EmailTemplates\EmailTemplateDtoInterface;
use App\PetDomain\VO\EmailRecipient;
use Mailjet\Client;
use Mailjet\Resources;

class EmailTemplateTemplateSender implements EmailTemplateSenderInterface
{
    /**
     * @var Client
     */
    private Client $emailSenderClient;
    /**
     * @var EmailRecipient|null
     */
    private ?EmailRecipient $from;

    public function __construct(Client $emailSenderClient, ?EmailRecipient $from = null)
    {
        $this->emailSenderClient = $emailSenderClient;
        $this->from = $from;
    }

    public function send(EmailTemplateDtoInterface $templateDto): void
    {
        $body = [
            'Messages' => [
                [
                    'From' => $this->getFrom($templateDto),
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

        if ($templateDto->hasVariables()) {
            array_merge(
                $body['Messages'][0],
                [
                    'Variables' => $templateDto->getVariables(),
                ]
            );
        }

        $this->emailSenderClient->post(
          Resources::$Email,
          [
              'body' => $body,
          ]);
    }

    private function getFrom(EmailTemplateDtoInterface $templateDto): array
    {
        if ($templateDto->hasFrom()) {
            return [
                'Email' => $templateDto->getFrom()->email(),
                'Name' => $templateDto->getFrom()->name(),
            ];
        }

        return [
            'Email' => $this->from->email(),
            'Name' => $this->from->name(),
        ];
    }
}
