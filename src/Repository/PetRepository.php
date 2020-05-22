<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pet[]    findAll()
 * @method Pet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    public function findBySearch(?string $breed, ?string $type, ?string $city): iterable
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($breed !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.breed', $queryBuilder->expr()->literal($breed)));
        }

        if ($city !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.city', $queryBuilder->expr()->literal($city)));
        }

        if ($type !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.type', $queryBuilder->expr()->literal($type)));
        }

        return $queryBuilder->orderBy('p.updatedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
