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
final class PetRepository extends ServiceEntityRepository implements PetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    public function findBySearch(?string $breed, ?string $type, ?string $city, ?bool $isLookingForNewOwner = false): iterable
    {
        $isLookingForNewOwner = $isLookingForNewOwner === true;
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder->andWhere($queryBuilder->expr()->eq('p.isLookingForOwner', ':isLookingForOwner'));
        $queryBuilder->setParameter('isLookingForOwner', $isLookingForNewOwner);

        if (!empty($breed)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.breed', $queryBuilder->expr()->literal($breed)));
        }

        if (!empty($city)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.city', $queryBuilder->expr()->literal($city)));
        }

        if (!empty($type)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.type', $queryBuilder->expr()->literal($type)));
        }

        return $queryBuilder->orderBy('p.updatedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getExistBreeds($petType): iterable
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder->select('pet.breed')
            ->from(Pet::class, 'pet')
            ->groupBy('pet.breed')
            ->where($queryBuilder->expr()->eq('pet.type', $queryBuilder->expr()->literal($petType)))
            ->getQuery()
            ->getResult();
    }

    public function getExistCities($petType): iterable
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder->select('pet.city')
            ->from(Pet::class, 'pet')
            ->groupBy('pet.city')
            ->where($queryBuilder->expr()->eq('pet.type', $queryBuilder->expr()->literal($petType)))
            ->andWhere($queryBuilder->expr()->isNotNull('pet.city'))
            ->andWhere($queryBuilder->expr()->neq('pet.city', "''"))
            ->getQuery()
            ->getResult();
    }
}
