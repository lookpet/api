<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\PetDomain\VO\Age;
use App\Repository\PetRepository;
use Cocur\Slugify\Slugify;
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
     * @ORM\OneToMany(targetEntity=PetComment::class, mappedBy="pet", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Breeder::class, inversedBy="pets")
     */
    private $breeder;

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

    public function __construct(string $type, ?string $slug, ?string $name = null, ?string $id = null, ?UserInterface $user = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->name = $name;

        if ($slug === null) {
            $this->generateSlug();
        } else {
            $this->slug = $slug;
        }

        if ($id === null) {
            $this->id = Uuid::uuid4()->toString();
        } else {
            $this->id = $id;
        }
        $this->media = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIsAlive(): ?bool
    {
        return $this->isAlive;
    }

    public function setIsAlive(bool $isAlive): self
    {
        $this->isAlive = $isAlive;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getIsLookingForOwner(): ?bool
    {
        return $this->isLookingForOwner;
    }

    public function setIsLookingForOwner(bool $isLookingForOwner): self
    {
        $this->isLookingForOwner = $isLookingForOwner;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getEyeColor(): ?string
    {
        return $this->eyeColor;
    }

    public function setEyeColor(?string $eyeColor): self
    {
        $this->eyeColor = $eyeColor;

        return $this;
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
            'isLookingForNewOwner' => $this->getIsLookingForOwner(),
            'price' => $this->getPrice(),
            'isFree' => $this->isFree(),
            'isSold' => $this->isSold(),
            'media' => $this->getMedia()->getValues(),
            'user' => $this->getUser(),
            'breeder' => $this->getBreeder(),
            'isAlive' => true,
//            'description' => $this->des
        ];
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function hasMedia(): bool
    {
        return $this->media->count() > 0;
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getFatherName(): ?string
    {
        return $this->fatherName;
    }

    public function setFatherName(?string $fatherName): self
    {
        $this->fatherName = $fatherName;

        return $this;
    }

    public function getMotherName(): ?string
    {
        return $this->motherName;
    }

    public function setMotherName(?string $motherName): self
    {
        $this->motherName = $motherName;

        return $this;
    }

    /**
     * @return Collection|PetLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PetLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
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
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
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

    public function addComment(PetComment $comment): self
    {
        $this->comments[] = $comment;

        return $this;
    }

    public function removeComment(PetComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    public function getBreeder(): ?Breeder
    {
        return $this->breeder;
    }

    public function setBreeder(?Breeder $breeder): self
    {
        $this->breeder = $breeder;

        return $this;
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

    public function setPlaceId(?string $placeId): self
    {
        $this->placeId = $placeId;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(?bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function isSold(): ?bool
    {
        return $this->isSold;
    }

    public function setIsSold(?bool $isSold): self
    {
        $this->isSold = $isSold;

        if ($this->isSold === true) {
            $this->isLookingForOwner = false;
            $this->isFree = false;
        }

        return $this;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function delete(): void
    {
        $this->isDeleted = true;
    }

    private function generateSlug(): void
    {
        $slugify = new Slugify();
        $slugEntropy = random_int(10, 100000);
        $this->slug = $slugify->slugify(implode('-', [$this->name, $slugEntropy]));
    }
}
