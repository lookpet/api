<?php

namespace App\Repository;

use App\Entity\PetComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PetComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetComment[]    findAll()
 * @method PetComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetComment::class);
    }

    // /**
    //  * @return PetComment[] Returns an array of PetComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PetComment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
