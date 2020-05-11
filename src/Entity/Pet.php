<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PetRepository::class)
 */
class Pet implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $isAlive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $breed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $about;

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
    private ?string $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $eyeColor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pets")
     */
    private $user;

    public function __construct(string $type, string $slug, ?string $name = null, ?string $id = null, ?UserInterface $user = null)
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
    }

    public function getId(): ?int
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
        ];
    }

    private function generateSlug(): void
    {
        $slugify = new Slugify();
        $slugEntropy = base_convert(rand(1000000000, PHP_INT_MAX), 10, 36);
        $this->slug = $slugify->slugify(implode('-', [$slugEntropy]));
    }
}
