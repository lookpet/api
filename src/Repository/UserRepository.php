<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findBySlug(string $slug): User
    {
        return $this->findOneBy([
            'slug' => $slug,
        ],
        [
            'updatedAt' => 'DESC',
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy([
            'email' => $email,
        ],
        [
            'updatedAt' => 'DESC',
        ]);
    }

    public function findUsersWithNoPets(): array
    {
        $users = $this->findAll();
        $result = [];

        foreach ($users as $user) {
            if (!$user->havePets()) {
                $result[] = $user;
            }
        }

        return $result;
    }
}
