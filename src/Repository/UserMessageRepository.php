<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method UserMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMessage[]    findAll()
 * @method UserMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMessageRepository extends ServiceEntityRepository implements UserMessageRepositoryInterface
{
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMessage::class);
        $this->entityManager = $registry->getManager();
    }

    public function getChatMessages(User $from, User $to): iterable
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('m.fromUser', ':fromUser'),
                $queryBuilder->expr()->eq('m.toUser', ':toUser')
            ))
            ->orWhere($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('m.fromUser', ':toUser'),
                $queryBuilder->expr()->eq('m.toUser', ':fromUser')
            ))->orderBy('m.createdAt')->groupBy('m.id');
        $queryBuilder->setParameter('fromUser', $from)
            ->setParameter('toUser', $to);

        return $queryBuilder->getQuery()->getResult();
    }

    public function save(UserMessage $userMessage): void
    {
        $this->entityManager->persist($userMessage);
        $this->entityManager->flush();
    }

    public function remove(UserMessage $userMessage): void
    {
        $this->entityManager->remove($userMessage);
        $this->entityManager->flush();
    }
}
