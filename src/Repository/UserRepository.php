<?php

namespace App\Repository;

use App\Entity\User;
use App\PetDomain\VO\Uuid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $registry->getManager();
    }

    public function findByUuid(Uuid $uuid): ?User
    {
        return $this->findOneBy([
            'id' => $uuid->__toString(),
        ]);
    }

    public function findBySlug(string $slug): ?User
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

    public function findUsersToNotifyNoPets(): iterable
    {
        return [];
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->join('u.pets', 'p');
        $queryBuilder->having('count(u.pets) = 0');
        $queryBuilder->where($queryBuilder->expr()->gte('u.lastNotificationDate', ':dateLastNotified'));
        $queryBuilder->setParameter('dateLastNotified', new \DateTimeImmutable('+1 day'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUsersToNotifyNewPetComments(): iterable
    {
        $queryBuilder = $this->createQueryBuilder('u');

        return $queryBuilder->join('u.pets', 'p')
            ->join('p.comments', 'pc')
            ->where($queryBuilder->expr()->lte('u.lastNotificationDate', 'pc.createdAt'))
            ->andWhere($queryBuilder->expr()->lte('u.nextNotificationAfterDate', ':dateNextNotification'))
            ->setParameter('dateNextNotification', new \DateTimeImmutable('now'))
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();
    }

    public function findUsersToNotifyPoll(): iterable
    {
        // TODO: Implement findUsersToNotifyPoll() method.
    }

    public function updateNotificationDate(User $user): void
    {
        $user->updateNotificationDate();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateNotificationAfterDate(User $user): void
    {
        $user->updateNotificationAfterDate();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
