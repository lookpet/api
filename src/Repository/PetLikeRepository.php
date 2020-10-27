<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Entity\PetLike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PetLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetLike[]    findAll()
 * @method PetLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetLikeRepository extends ServiceEntityRepository implements PetLikeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetLike::class);
    }

    public function getUserPetLike(User $user, Pet $pet): ?PetLike
    {
        $petLikes = $this->findBy([
            'pet' => $pet,
            'user' => $user,
        ]);

        if (count($petLikes) === 0) {
            return null;
        }

        return array_pop($petLikes);
    }
}
