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

    public function get(): int
    {
        return $this->limit;
    }
}
