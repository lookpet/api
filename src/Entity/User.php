<?php

namespace App\Entity;

use App\Dto\Authentication\UserLoginDto;
use App\Dto\User\UserDto;
use App\Entity\Traits\LifecycleCallbackTrait;
use App\Entity\Traits\TimestampTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \JsonSerializable
{
    use LifecycleCallbackTrait;
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     * @Groups("main")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("main")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=ApiToken::class, mappedBy="user", orphanRemoval=true)
     */
    private $apiTokens;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Pet::class, mappedBy="user")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    private $pets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=PetComment::class, mappedBy="user")
     */
    private $petComments;

    /**
     * @ORM\OneToMany(targetEntity=PetLike::class, mappedBy="user", orphanRemoval=true)
     */
    private $petLikes;

    /**
     * @ORM\ManyToOne(targetEntity=Breeder::class, inversedBy="users")
     */
    private $breeder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $providerId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $providerLastResponse;

    /**
     * @ORM\OneToMany(targetEntity=MediaUser::class, mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $placeId;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastNotificationDate;

    /**
     * @ORM\OneToMany(targetEntity=UserEvent::class, mappedBy="user")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="user")
     */
    private $postLikes;

    public function __construct(string $id = null, ?string $slug = null, ?string $firstName = null)
    {
        $this->slug = $slug;
        $this->firstName = $firstName;
        $this->id = $id ?? Uuid::uuid4()->toString();

        $this->apiTokens = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->pets = new ArrayCollection();
        $this->petComments = new ArrayCollection();
        $this->petLikes = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
    }

    public static function createFromLoginDto(UserLoginDto $userLoginDto): self
    {
        return (new self(
            $userLoginDto->getId()
        ))->setEmail($userLoginDto->getEmail())
            ->setPassword($userLoginDto->getPassword());
    }

    public function updateFromDto(UserDto $userDto): void
    {
        if ($userDto->getSlug() !== null) {
            $this->slug = $userDto->getSlug();
        }

        if ($userDto->getFirstName() !== null) {
            $this->firstName = $userDto->getFirstName();
        }

        if ($userDto->getLastName() !== null) {
            $this->lastName = $userDto->getLastName();
        }

        if ($userDto->getDescription() !== null) {
            $this->description = $userDto->getDescription();
        }

        if ($userDto->getCity() !== null) {
            $this->city = $userDto->getCity();
        }

        if ($userDto->getPlaceId() !== null) {
            $this->placeId = $userDto->getPlaceId();
        }

        if ($userDto->getPhone() !== null) {
            $this->phone = $userDto->getPhone();
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using bcrypt or argon
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): string
    {
        return $this->firstName === null ? '' : $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|ApiToken[]
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function getActiveApiToken(): ?ApiToken
    {
        foreach ($this->apiTokens as $apiToken) {
            if (!$apiToken->isExpired()) {
                return $apiToken;
            }
        }

        return null;
    }

    public function hasActiveApiToken(): bool
    {
        return $this->getActiveApiToken() !== null;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens[] = $apiToken;
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        if ($this->media->count() !== 0) {
            return $this->media->first()->getMedia()->getPublicUrl();
        } elseif ($this->isFaceBook()) {
            $lastResponse = $this->getProviderLastResponse();
            if (isset($lastResponse['profile']['picture']['data']['url'])) {
                return $lastResponse['profile']['picture']['data']['url'];
            }
        }

        return null;
    }

    public function isFaceBook(): bool
    {
        return $this->provider === 'facebook';
    }

    public function isLookPetUser(): bool
    {
        $domain = mb_substr($this->getEmail(), mb_strpos($this->getEmail(), '@') + 1);

        return $domain === 'look.pet';
    }

    public function hasEmail(): bool
    {
        return $this->email !== null;
    }

    public function allowSendEmailNotifications(): bool
    {
        return $this->hasEmail()
            && !$this->isLookPetUser();
    }

    public function jsonSerialize(): array
    {
        return [
            'slug' => $this->getSlug(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'name' => $this->getName(),
            'provider' => $this->getProvider(),
            'providerId' => $this->getProviderId(),
            'phone' => $this->getPhone(),
            'description' => $this->getDescription(),
            'city' => $this->getCity(),
            'placeId' => $this->getPlaceId(),
            'avatar' => $this->getAvatarUrl(),
            'media' => $this->getMedia()->getValues(),
            'breeder' => $this->getBreeder(),
            'hasPets' => count($this->getPets()) === 0 ? false : true,
            'email' => $this->getEmail(),
        ];
    }

    /**
     * @return Collection|Pet[]
     */
    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function havePets(): bool
    {
        return count($this->pets);
    }

    public function addPet(Pet $pet): self
    {
        if (!$this->pets->contains($pet)) {
            $this->pets[] = $pet;
            $pet->setUser($this);
        }

        return $this;
    }

    public function removePet(Pet $pet): self
    {
        if ($this->pets->contains($pet)) {
            $this->pets->removeElement($pet);
            // set the owning side to null (unless already changed)
            if ($pet->getUser() === $this) {
                $pet->setUser(null);
            }
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

    /**
     * @return Collection|PetComment[]
     */
    public function getPetComments(): Collection
    {
        return $this->petComments;
    }

    public function addPetComment(PetComment $petComment): self
    {
        if (!$this->petComments->contains($petComment)) {
            $this->petComments[] = $petComment;
            $petComment->setUser($this);
        }

        return $this;
    }

    public function removePetComment(PetComment $petComment): self
    {
        if ($this->petComments->contains($petComment)) {
            $this->petComments->removeElement($petComment);
            // set the owning side to null (unless already changed)
            if ($petComment->getUser() === $this) {
                $petComment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PetLike[]
     */
    public function getPetLikes(): Collection
    {
        return $this->petLikes;
    }

    public function addPetLike(PetLike $petLike): self
    {
        if (!$this->petLikes->contains($petLike)) {
            $this->petLikes[] = $petLike;
            $petLike->setUser($this);
        }

        return $this;
    }

    public function removePetLike(PetLike $petLike): self
    {
        if ($this->petLikes->contains($petLike)) {
            $this->petLikes->removeElement($petLike);
            // set the owning side to null (unless already changed)
            if ($petLike->getUser() === $this) {
                $petLike->setUser(null);
            }
        }

        return $this;
    }

    public function hasBreeder(): bool
    {
        return $this->breeder !== null;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name ?? $this->firstName;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function setProviderId(?string $providerId): self
    {
        $this->providerId = $providerId;

        return $this;
    }

    public function getProviderLastResponse(): ?array
    {
        if ($this->providerLastResponse !== null) {
            return json_decode($this->providerLastResponse, true);
        }

        return null;
    }

    public function setProviderLastResponse(?string $providerLastResponse): self
    {
        $this->providerLastResponse = $providerLastResponse;

        return $this;
    }

    /**
     * @return Collection|MediaUser[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(MediaUser $mediaUser): self
    {
        if (!$this->media->contains($mediaUser)) {
            $this->media[] = $mediaUser;
        }

        return $this;
    }

    public function removeMedia(MediaUser $mediaUser): self
    {
        if ($this->media->contains($mediaUser)) {
            $this->media->removeElement($mediaUser);
            // set the owning side to null (unless already changed)
            if ($mediaUser->getUser() === $this) {
                $mediaUser->setUser(null);
            }
        }

        return $this;
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

    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    public function getLastNotificationDate(): ?\DateTimeInterface
    {
        return $this->lastNotificationDate;
    }

    public function setLastNotificationDate(?\DateTimeInterface $lastNotificationDate): self
    {
        $this->lastNotificationDate = $lastNotificationDate;

        return $this;
    }

    public function updateNotificationDate(): void
    {
        $this->lastNotificationDate = new \DateTimeImmutable();
    }

    public function hasNotificationSentToday(): bool
    {
        return $this->lastNotificationDate !== null;
    }

    /**
     * @return Collection|UserEvent[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(UserEvent $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(UserEvent $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getPostLikes(): Collection
    {
        return $this->postLikes;
    }

    public function addPostLike(PostLike $postLike): self
    {
        if (!$this->postLikes->contains($postLike)) {
            $this->postLikes[] = $postLike;
            $postLike->setUser($this);
        }

        return $this;
    }

    public function removePostLike(PostLike $postLike): self
    {
        if ($this->postLikes->removeElement($postLike)) {
            // set the owning side to null (unless already changed)
            if ($postLike->getUser() === $this) {
                $postLike->setUser(null);
            }
        }

        return $this;
    }
}
