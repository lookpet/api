<?php

namespace App\Service;

use App\Entity\Pet;
use App\PetDomain\VO\Age;
use Symfony\Contracts\Translation\TranslatorInterface;

class AgeCalculator implements AgeCalculatorInterface
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getAge(\DateTimeInterface $dateTime): ?string
    {
        $age = new Age($dateTime);

        if ($age === null) {
            return null;
        }

        $result = [];

        if ($age->hasYears()) {
            $result[] = $this->translator->trans('num_of_years', [
                'years' => $age->getYears(),
            ]);
        }

        if ($age->hasMonths()) {
            $result[] = $this->translator->trans('num_of_months', [
                'months' => $age->getMonths(),
            ]);
        }

        if (!$age->hasYears() && !$age->hasMonths() && $age->hasDays()) {
            $result[] = $this->translator->trans('num_of_days', [
                'days' => $age->getDays(),
            ]);
        }

        return implode(' ', $result);
    }
}
