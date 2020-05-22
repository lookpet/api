<?php

declare(strict_types=1);

namespace App\Dto;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

class AuthenticationUserLoginDto
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     *
     * @SWG\Property(
     *     type="string",
     *     description="email for authentication",
     *     example="zombiemelon@look.pet",
     * )
     */
    private string $email;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="password for authentication",
     *     example="supersecurepassword",
     * )
     */
    private string $password;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
