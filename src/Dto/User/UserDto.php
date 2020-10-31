<?php

declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\Breeder;
use Swagger\Annotations as SWG;

final class UserDto
{
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
     * @SWG\Property(
     *     type="string",
     *     description="pet date of birth",
     *     example="2020-01-01",
     * )
     */
    private ?\DateTimeInterface $dateOfBirth = null;

    private ?Breeder $breeder = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): void
    {
        $this->placeId = $placeId;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getBreeder(): ?Breeder
    {
        return $this->breeder;
    }

    public function setBreeder(?Breeder $breeder): void
    {
        $this->breeder = $breeder;
    }
}
