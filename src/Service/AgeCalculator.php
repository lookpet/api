<?php

namespace App\Service;

use App\Entity\Pet;
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

    public function getAge(Pet $pet): ?string
    {
        $age = $pet->getAge();
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

        return implode(' ', $result);
    }
}
