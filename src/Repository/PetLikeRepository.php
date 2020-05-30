<?php

namespace App\Repository;

use App\Entity\PetLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PetLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetLike[]    findAll()
 * @method PetLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetLike::class);
    }

    // /**
    //  * @return PetLike[] Returns an array of PetLike objects
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
    public function findOneBySomeField($value): ?PetLike
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
