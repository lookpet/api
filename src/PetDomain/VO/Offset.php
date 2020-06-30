<?php


namespace App\PetDomain\VO;


class Offset
{
    private PageNumber $pageNumber;
    private Limit $limit;
    private int $offset;

    public function __construct(PageNumber $pageNumber, Limit $limit)
    {
        $this->offset = 0;
        $this->pageNumber = $pageNumber;
        $this->limit = $limit;
        if (!$this->pageNumber->isFirst()){
            $this->offset = $this->pageNumber->previous() * $this->limit->get();
        }
    }

    public function get():int
    {
        return $this->offset;
    }

    public function limit(): Limit
    {
        return $this->limit;
    }

    public function pageNumber(): PageNumber
    {
        return $this->pageNumber;
    }
}