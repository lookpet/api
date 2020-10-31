<?php

declare(strict_types=1);

namespace App\Dto\Authentication;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

class UserLoginDto
{
    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet id",
     *     example="dog",
     * )
     */
    private ?string $id = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user nickname or alias that will be unique identifier",
     *     example="slavian-old-baklazhan",
     * )
     */
    private ?string $slug = null;

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

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user first name",
     *     example="Svetoslav",
     * )
     */
    private ?string $firstName;

    public function __construct(string $email, string $password, ?string $firstName = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
