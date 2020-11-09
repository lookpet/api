<?php

namespace App\Repository;

use App\Entity\User;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Uuid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
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

    /**
     * {@inheritdoc}
     */
    public function findUsersToNotifyNoPets(): iterable
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder->leftJoin('u.pets', 'p')
            ->leftJoin('u.events', 'e', Expr\Join::WITH, 'e.type = :noPetNotification')
            ->where($queryBuilder->expr()->isNull('p.id'))
            ->andWhere($queryBuilder->expr()->isNull('e.id'))
            ->andWhere($queryBuilder->expr()->lte('u.nextNotificationAfterDate', ':dateNextNotification'))
            ->setParameter('dateNextNotification', new \DateTimeImmutable('now'))
            ->setParameter('noPetNotification', EventType::NO_PET_NOTIFICATION);

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
