<?php

namespace App\PetDomain\VO;

final class Age
{
    private \DateInterval $nowToDateInterval;

    public function __construct(\DateTimeInterface $dateTimeImmutable)
    {
        $this->nowToDateInterval = $dateTimeImmutable->diff(new \DateTimeImmutable('now'));
    }

    public function hasYears(): bool
    {
        return (bool) $this->nowToDateInterval->y > 0;
    }

    public function getYears(): int
    {
        return $this->nowToDateInterval->y;
    }

    public function hasMonths(): bool
    {
        return (bool) $this->nowToDateInterval->m > 0;
    }

    public function getMonths(): int
    {
        return $this->nowToDateInterval->m;
    }

    public function hasDays(): bool
    {
        return (bool) $this->nowToDateInterval->d > 0;
    }

    public function getDays(): int
    {
        return $this->nowToDateInterval->d;
    }
}
