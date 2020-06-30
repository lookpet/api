<?php


namespace App\PetDomain\VO;


class PageNumber
{
    private int $pageNumber;

    public function __construct(?int $pageNumber = 1)
    {
        if ($pageNumber === null) {
            $pageNumber = 1;
        }

        $this->pageNumber = $pageNumber;
    }

    public function isFirst(): bool
    {
        return $this->pageNumber === 1;
    }

    public function previous(): int
    {
        return $this->pageNumber-1;
    }

    public function get(): int
    {
        return $this->pageNumber;
    }
}