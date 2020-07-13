<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Pet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PetResponseBuilder implements PetResponseBuilderInterface
{
    /**
     * @var AgeCalculatorInterface
     */
    private AgeCalculatorInterface $ageCalculator;

    public function __construct(AgeCalculatorInterface $ageCalculator, TranslatorInterface $translator)
    {
        $this->ageCalculator = $ageCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function build(?UserInterface $user, Pet ...$pets): JsonResponse
    {
        $result = [];

        if (count($pets) !== 0) {
            foreach ($pets as $pet) {
                $result[] = array_merge(
                    $pet->jsonSerialize(),
                    [
                        'hasLike' => $pet->hasLike($user),
                        'age' => $this->ageCalculator->getAge($pet->getDateOfBirth()),
                    ]
                );
            }
        }

        return new JsonResponse([
            'pets' => $result,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForOnePet(Pet $pet, ?UserInterface $user): JsonResponse
    {
        return new JsonResponse(array_merge(
            $pet->jsonSerialize(),
            [
                'hasLike' => $pet->hasLike($user),
                'age' => $this->ageCalculator->getAge($pet->getDateOfBirth()),
            ]
        ));
    }
}
