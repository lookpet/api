<?php

declare(strict_types=1);

namespace App\Entity\Traits;

trait LifecycleCallbackTrait
{
    /**
     * @ORM\PrePersist
     */
    final public function prePersist(): void
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }

        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * @ORM\PreUpdate
     */
    final public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    abstract public function setCreatedAt(\DateTimeInterface $createdAt);

    abstract public function setUpdatedAt(\DateTimeInterface $updatedAt);
}
