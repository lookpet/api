<?php

namespace App\Message;

use App\PetDomain\VO\Id;

class MailNoPetMessage
{
    private Id $uuid;

    public function __construct(Id $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): Id
    {
        return $this->uuid;
    }
}
