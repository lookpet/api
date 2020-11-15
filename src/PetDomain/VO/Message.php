<?php

namespace App\PetDomain\VO;

final class Message
{
    private string $message;

    public function __construct(string $message)
    {
        if (empty($message)) {
            throw new \LogicException('Message cannot be empty');
        }

        $this->message = $message;
    }

    public static function create(string $message): self
    {
        return new static($message);
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
