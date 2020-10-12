<?php

namespace App\Entity;

use App\Dto\Pet\PetDto;
use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Age;
use App\Repository\PetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PetRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Pet implements \JsonSerializable
{
    use TimestampTrait;
    use LifecycleCallbackTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAlive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $breed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isLookingForOwner = false;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eyeColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fatherName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motherName;

    /**
     * @ORM\OneToMany(targetEntity=PetLike::class, mappedBy="pet")
     */
    private $likes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $placeId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFree;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSold;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pets")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Media::class)
     * @ORM\JoinTable(name="pet_media",
     *      joinColumns={@ORM\JoinColumn(name="pet_id", referencedColumnName="id")},
     * )
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity=PetComment::class, mappedBy="pet", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Breeder::class, inversedBy="pets")
     */
    private $breeder;

    public function __construct(string $type, ?string $slug, ?string $id = null, ?string $name = null, ?UserInterface $user = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->name = $name;

        if ($slug === null) {
            throw new \LogicException('Slug cannot be empty');
        }

        $this->slug = $slug;

        if ($id === null) {
            $id = Uuid::uuid4()->toString();
        }

        $this->id = $id;
        $this->media = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function updateFromDto(PetDto $petDto, ?User $user = null): void
    {
        $this->setUser($user);

        if ($petDto->getType() !== null) {
            $this->type = $petDto->getType();
        }

        if ($petDto->getName() !== null) {
            $this->name = $petDto->getName();
        }

        if ($petDto->getSlug() !== null) {
            $this->slug = $petDto->getSlug();
        }

        if ($petDto->getCity() !== null) {
            $this->city = $petDto->getCity();
            if ($petDto->getPlaceId() !== null) {
                $this->placeId = $petDto->getPlaceId();
            }
        }

        if ($petDto->getBreed() !== null) {
            $this->breed = $petDto->getBreed();
        }

        if ($petDto->getGender() !== null) {
            $this->gender = $petDto->getGender();
        }

        if ($petDto->getPrice() !== null) {
            $this->price = $petDto->getPrice();
        }

        if ($petDto->getFatherName() !== null) {
            $this->fatherName = $petDto->getFatherName();
        }

        if ($petDto->getMotherName() !== null) {
            $this->motherName = $petDto->getMotherName();
        }

        if ($petDto->getColor() !== null) {
            $this->color = $petDto->getColor();
        }

        if ($petDto->getEyeColor() !== null) {
            $this->eyeColor = $petDto->getEyeColor();
        }

        if ($petDto->getAbout() !== null) {
            $this->about = $petDto->getAbout();
        }

        if ($petDto->getDateOfBirth() !== null) {
            $this->dateOfBirth = $petDto->getDateOfBirth();
        }

        if (count($petDto->getMedia()) > 0) {
            $this->addMedia(...$petDto->getMedia());
        }

        if (count($petDto->getComments()) > 0) {
            $this->addComments(...$petDto->getComments());
        }

        if (count($petDto->getPetLikes()) > 0) {
            $this->addLikes(...$petDto->getPetLikes());
        }

        if ($petDto->isAlive() !== null) {
            $this->isAlive = $petDto->isAlive();
        }

        if ($petDto->isFree() !== null) {
            $this->isFree = $petDto->isFree();
        }

        if ($isLookingForNewOwner = $petDto->isLookingForNewOwner() !== null) {
            $this->isLookingForOwner = $isLookingForNewOwner;
        }

        if ($petDto->isSold() !== null) {
            $this->isSold = $petDto->isSold();
            if ($this->isSold === true) {
                $this->isLookingForOwner = false;
                $this->isFree = false;
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'slug' => $this->getSlug(),
            'name' => $this->getName(),
            'city' => $this->getCity(),
            'placeId' => $this->getPlaceId(),
            'breed' => $this->getBreed(),
            'fatherName' => $this->getFatherName(),
            'motherName' => $this->getMotherName(),
            'color' => $this->getColor(),
            'eyeColor' => $this->getEyeColor(),
            'dateOfBirth' => $this->getDateOfBirth(),
            'about' => $this->getAbout(),
            'gender' => $this->getGender(),
            'likes' => count($this->getLikes()),
            'comments' => $this->getComments()->toArray(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'isLookingForNewOwner' => $this->isLookingForOwner(),
            'price' => $this->getPrice(),
            'isFree' => $this->isFree(),
            'isSold' => $this->isSold(),
            'media' => $this->getMedia()->getValues(),
            'user' => $this->getUser(),
            'breeder' => $this->getBreeder(),
            'isAlive' => true,
        ];
    }

    public function equals(self $pet): bool
    {
        return $pet->getId() === $this->getId();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function isAlive(): ?bool
    {
        return $this->isAlive;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function isLookingForOwner(): bool
    {
        return $this->isLookingForOwner;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getEyeColor(): ?string
    {
        return $this->eyeColor;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getFatherName(): ?string
    {
        return $this->fatherName;
    }

    public function getMotherName(): ?string
    {
        return $this->motherName;
    }

    public function getBreeder(): ?Breeder
    {
        return $this->breeder;
    }

    public function getAge(): ?Age
    {
        if ($this->dateOfBirth === null) {
            return null;
        }

        return new Age($this->dateOfBirth);
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function isFree(): ?bool
    {
        return $this->isFree;
    }

    public function isSold(): ?bool
    {
        return $this->isSold;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function delete(): void
    {
        $this->isDeleted = true;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media ...$mediaCollection): self
    {
        foreach ($mediaCollection as $media) {
            if (!$this->media->contains($media)) {
                $this->media[] = $media;
            }
        }

        return $this;
    }

    public function removeMedia(Media $medium): self
    {
        if ($this->media->contains($medium)) {
            $this->media->removeElement($medium);
        }

        return $this;
    }

    /**
     * @return Collection|PetComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComments(PetComment ...$comments): self
    {
        foreach ($comments as $comment) {
            if (!$this->comments->contains($comment)) {
                $this->comments[] = $comment;
            }
        }

        return $this;
    }

    public function removeComments(PetComment ...$comments): self
    {
        foreach ($comments as $comment) {
            if ($this->comments->contains($comment)) {
                $this->comments->removeElement($comment);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PetLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLikes(PetLike ...$likes): self
    {
        foreach ($likes as $like) {
            if (!$this->likes->contains($like)) {
                $this->likes[] = $like;
            }
        }

        return $this;
    }

    public function hasLike(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        /** @var PetLike $currentLike */
        foreach ($this->likes as $currentLike) {
            if ($currentLike->getUser()->equals($user)) {
                return true;
            }
        }

        return false;
    }

    public function removeLike(PetLike ...$like): self
    {
        /** @var PetLike $like */
        foreach ($this->likes as $like) {
            if ($like->equals($like)) {
                $this->likes->removeElement($like);
            }
        }

        return $this;
    }
}
