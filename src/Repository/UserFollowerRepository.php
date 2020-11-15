<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserFollower;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method UserFollower|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFollower|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFollower[]    findAll()
 * @method UserFollower[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFollowerRepository extends ServiceEntityRepository implements UserFollowerRepositoryInterface
{
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFollower::class);
        $this->entityManager = $registry->getManager();
    }

    public function getUserFollower(User $user, User $follower): ?UserFollower
    {
        $userFollowers = $this->findBy([
            'follower' => $follower,
            'user' => $user,
        ]);

        if (count($userFollowers) === 0) {
            return null;
        }

        return array_pop($userFollowers);
    }

    public function save(UserFollower $userFollower): void
    {
        $this->entityManager->persist($userFollower);
        $this->entityManager->flush();
    }

    public function remove(UserFollower $userFollower): void
    {
        $this->entityManager->remove($userFollower);
        $this->entityManager->flush();
    }
}
