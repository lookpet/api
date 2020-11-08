<?php

namespace App\Message;

use App\PetDomain\VO\Uuid;

class MailWelcomeMessage
{
    private Uuid $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }
}
