<?php

declare(strict_types=1);

namespace App\Dto\Pet;

use App\Entity\Media;
use Swagger\Annotations as SWG;

final class PetDto
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
     *     description="pet type",
     *     example="dog",
     * )
     */
    private string $type;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet nickname or alias that will be unique identifier of the pet",
     *     example="rex2020",
     * )
     */
    private ?string $slug;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet name",
     *     example="rex",
     * )
     */
    private ?string $name;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet breed",
     *     example="Husky",
     * )
     */
    private ?string $breed;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet color",
     *     example="Black and white",
     * )
     */
    private ?string $color;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet eye color",
     *     example="Blue",
     * )
     */
    private ?string $eyeColor;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet date of birth",
     *     example="2020-01-01",
     * )
     */
    private ?\DateTimeInterface $dateOfBirth;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet gender",
     *     example="male",
     * )
     */
    private ?string $gender;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet additional information",
     *     example="My pet is the Best!",
     * )
     */
    private ?string $about;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is looking for new owner",
     *     example="true",
     * )
     */
    private bool $isLookingForNewOwner = false;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet father",
     *     example="Husky Haven Super Father",
     * )
     */
    private string $fatherName;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet mother",
     *     example="Husky Haven Super Mother",
     * )
     */
    private string $motherName;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="city",
     *     example="Moscow",
     * )
     */
    private string $city;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="place_id",
     *     example="",
     * )
     */
    private string $placeId;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="place_id",
     *     example="10000$",
     * )
     */
    private string $price;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is free",
     *     example="true",
     * )
     */
    private bool $isFree = false;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is sold",
     *     example="true",
     * )
     */
    private bool $isSold = false;

    /**
     * @var Media[]
     */
    private array $media = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): void
    {
        $this->breed = $breed;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getEyeColor(): ?string
    {
        return $this->eyeColor;
    }

    public function setEyeColor(?string $eyeColor): void
    {
        $this->eyeColor = $eyeColor;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): void
    {
        $this->about = $about;
    }

    public function isLookingForNewOwner(): bool
    {
        return $this->isLookingForNewOwner;
    }

    public function setIsLookingForOwner(bool $isLookingForNewOwner): void
    {
        $this->isLookingForNewOwner = $isLookingForNewOwner;
    }

    public function getFatherName(): string
    {
        return $this->fatherName;
    }

    public function setFatherName(string $fatherName): void
    {
        $this->fatherName = $fatherName;
    }

    public function getMotherName(): string
    {
        return $this->motherName;
    }

    public function setMotherName(string $motherName): void
    {
        $this->motherName = $motherName;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    public function setPlaceId(string $placeId): void
    {
        $this->placeId = $placeId;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function isSold(): bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): void
    {
        $this->isSold = $isSold;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function setIsFree(bool $isFree): void
    {
        $this->isFree = $isFree;
    }

    public function getMedia(): array
    {
        return $this->media;
    }

    public function setMedia(Media ...$media): void
    {
        $this->media = $media;
    }
}
