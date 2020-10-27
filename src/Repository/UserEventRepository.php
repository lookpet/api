<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserEvent;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEvent[]    findAll()
 * @method UserEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEventRepository extends ServiceEntityRepository implements UserEventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEvent::class);
    }

    public function log(EventType $type, User $user, Utm $utm, ?EventContext $context = null): void
    {
        $userEvent = new UserEvent($type, $user, $utm);
        $this->getEntityManager()->persist($userEvent);
        $this->getEntityManager()->flush();
    }
}
