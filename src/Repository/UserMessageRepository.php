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

    public function getChatLastMessages(User $user): iterable
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->where($queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq('m.fromUser', ':fromUser'),
            $queryBuilder->expr()->eq('m.toUser', ':toUser')
        ))
            ->orderBy('m.createdAt', 'DESC')
            ->groupBy('m.fromUser')
            ->addGroupBy('m.toUser');

        $queryBuilder->setParameter('fromUser', $user)
            ->setParameter('toUser', $user);

        /** @var UserMessage[] $userMessages */
        $userMessages = $queryBuilder->getQuery()->getResult();

        /** @var UserMessage[] $result */
        $result = [];

        foreach ($userMessages as $userMessage) {
            var_dump(
                $userMessage->getFromUser()->getUsername(),
                $userMessage->getToUser()->getUsername(),
                $userMessage->getMessage(),
                $userMessage->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        foreach ($userMessages as $userMessage) {
            foreach ($result as $resultUserMessage) {
                if (
                    $resultUserMessage->getToUser()->equals($userMessage->getFromUser()) &&
                    $resultUserMessage->getFromUser()->equals($userMessage->getToUser())
                ) {
                    continue 2;
                }
            }
            $result[] = $userMessage;
        }

        return $result;
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

    public function readMessages(User $from, User $to): void
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->update(UserMessage::class)
            ->where($queryBuilder->expr()->andX(
            $queryBuilder->expr()->eq('m.fromUser', ':fromUser'),
            $queryBuilder->expr()->eq('m.toUser', ':toUser')
        ))
            ->orWhere($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('m.fromUser', ':toUser'),
                $queryBuilder->expr()->eq('m.toUser', ':fromUser')
            ))->orderBy('m.createdAt')->groupBy('m.id');
        $queryBuilder->setParameter('fromUser', $from)
            ->setParameter('toUser', $to);

        $queryBuilder->getQuery()->execute();
    }
}
