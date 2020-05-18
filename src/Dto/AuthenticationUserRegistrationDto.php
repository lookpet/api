<?php

declare(strict_types=1);

namespace App\Dto;

use Swagger\Annotations as SWG;

final class AuthenticationUserRegistrationDto extends AuthenticationUserLoginDto
{
    /**
     * @SWG\Property(
     *     type="string",
     *     description="user first name",
     *     example="Svetoslav",
     * )
     */
    private ?string $firstName;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }
}