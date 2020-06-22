<?php

namespace App\PetDomain\VO;

final class Age implements \JsonSerializable
{
    /** @var \DateInterval */
    private $nowToDateInterval;

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

    public function hasMonths(): int
    {
        return (bool) $this->nowToDateInterval->m > 0;
    }

    public function getMonths(): int
    {
        return $this->nowToDateInterval->m;
    }

    public function jsonSerialize()
    {
        return '3 года 6 месяцев';
//        '
//        {% if pet.age is not null and pet.isAlive == true %}
//    {% if pet.age.hasYears == true %}
//        {{ \'num_of_years\' | trans({
//            \'%years%\': pet.age.years
//        }) }}
//    {% endif %}
//    {% if pet.age.hasMonths == true %}
//        {{ \'num_of_months\' | trans({
//            \'%months%\': pet.age.months
//        }) }}
//    {% endif %}
//{% endif %}
//        ';
    }
}
