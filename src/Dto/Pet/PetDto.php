<?php

declare(strict_types=1);

namespace App\Dto\Pet;

use App\Entity\Breeder;
use App\Entity\Media;
use App\Entity\PetComment;
use App\Entity\PetLike;
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
    private ?string $type = null;

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
     *     description="pet name",
     *     example="rex",
     * )
     */
    private ?string $name = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet breed",
     *     example="Husky",
     * )
     */
    private ?string $breed = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet color",
     *     example="Black and white",
     * )
     */
    private ?string $color = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet eye color",
     *     example="Blue",
     * )
     */
    private ?string $eyeColor = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet date of birth",
     *     example="2020-01-01",
     * )
     */
    private ?\DateTimeInterface $dateOfBirth = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet gender",
     *     example="male",
     * )
     */
    private ?string $gender = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet additional information",
     *     example="My pet is the Best!",
     * )
     */
    private ?string $about = null;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is looking for new owner",
     *     example="true",
     * )
     */
    private ?bool $isLookingForNewOwner = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet father",
     *     example="Husky Haven Super Father",
     * )
     */
    private ?string $fatherName = null;

    /**
     * @SWG\Property(
     *     type="string",
     *     description="pet mother",
     *     example="Husky Haven Super Mother",
     * )
     */
    private ?string $motherName = null;

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
     *     description="place_id",
     *     example="10000$",
     * )
     */
    private ?string $price = null;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is free",
     *     example="true",
     * )
     */
    private ?bool $isFree = null;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is sold",
     *     example="true",
     * )
     */
    private ?bool $isSold = null;

    /**
     * @SWG\Property(
     *     type="bool",
     *     description="whether the pet is alive",
     *     example="true",
     * )
     */
    private ?bool $isAlive = null;

    /**
     * @var Media[]
     */
    private array $media = [];

    /**
     * @var PetComment[]
     */
    private array $comments = [];

    /**
     * @var PetLike[]
     */
    private array $petLikes = [];

    private ?Breeder $breeder = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
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

    public function isLookingForNewOwner(): ?bool
    {
        return $this->isLookingForNewOwner;
    }

    public function setIsLookingForOwner(bool $isLookingForNewOwner): void
    {
        $this->isLookingForNewOwner = $isLookingForNewOwner;
    }

    public function getFatherName(): ?string
    {
        return $this->fatherName;
    }

    public function setFatherName(?string $fatherName): void
    {
        $this->fatherName = $fatherName;
    }

    public function getMotherName(): ?string
    {
        return $this->motherName;
    }

    public function setMotherName(?string $motherName): void
    {
        $this->motherName = $motherName;
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

    /**
     * @return string
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(?string $price): void
    {
        $this->price = $price;
    }

    public function isSold(): ?bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): void
    {
        $this->isSold = $isSold;
    }

    public function isFree(): ?bool
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

    public function getBreeder(): ?Breeder
    {
        return $this->breeder;
    }

    public function setBreeder(?Breeder $breeder): void
    {
        $this->breeder = $breeder;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    public function getPetLikes(): array
    {
        return $this->petLikes;
    }

    public function setPetLikes(array $petLikes): void
    {
        $this->petLikes = $petLikes;
    }

    public function isAlive(): ?bool
    {
        return $this->isAlive;
    }

    public function setIsAlive(?bool $isAlive): void
    {
        $this->isAlive = $isAlive;
    }
}
