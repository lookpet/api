<?php

namespace App\PetDomain\VO;

class EmailRecipient
{
    private string $email;
    private string $name;

    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public static function create(string $email, string $name): self
    {
        return new self($email, $name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }
}
