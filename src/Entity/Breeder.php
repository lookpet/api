<?php

namespace App\Entity;

use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\Repository\BreederRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BreederRepository::class)
 */
class Breeder implements \JsonSerializable
{
    use TimestampTrait;
    use LifecycleCallbackTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Pet::class, mappedBy="breeder")
     */
    private $pets;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="breeder")
     */
    private $users;

    public function __construct(string $name)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->pets = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * @return Collection|Pet[]
     */
    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function addPet(Pet $pet): self
    {
        if (!$this->pets->contains($pet)) {
            $this->pets[] = $pet;
            $pet->setBreeder($this);
        }

        return $this;
    }

    public function removePet(Pet $pet): self
    {
        if ($this->pets->contains($pet)) {
            $this->pets->removeElement($pet);
            // set the owning side to null (unless already changed)
            if ($pet->getBreeder() === $this) {
                $pet->setBreeder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setBreeder($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getBreeder() === $this) {
                $user->setBreeder(null);
            }
        }

        return $this;
    }
}
