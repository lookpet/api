<?php

declare(strict_types=1);

namespace App\Dto\User;

use Swagger\Annotations as SWG;

final class UserDto
{
    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet id",
     *     example="dog",
     * )
     */
    private string $id;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet nickname or alias that will be unique identifier of the pet",
     *     example="rex2020",
     * )
     */
    private ?string $slug = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user first name",
     *     example="Svetoslav",
     * )
     */
    private ?string $firstName = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user last name",
     *     example="Smith",
     * )
     */
    private ?string $lastName = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user phone",
     *     example="+79999999999",
     * )
     */
    private ?string $phone = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="user description",
     *     example="Lorem ipsum",
     * )
     */
    private ?string $description = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="city",
     *     example="Moscow",
     * )
     */
    private ?string $city = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="place_id",
     *     example="",
     * )
     */
    private ?string $placeId = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
