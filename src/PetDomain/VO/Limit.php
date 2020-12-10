<?php

namespace App\PetDomain\VO;

class Limit
{
    private const DEFAULT_LIMIT = 10;
    private int $limit;

    public function __construct(int $limit = self::DEFAULT_LIMIT)
    {
        $this->limit = $limit;
    }

    public static function create(int $limit = self::DEFAULT_LIMIT): self
    {
        return new static($limit);
    }

    public function get(): int
    {
        return $this->limit;
    }
}
