<?php

namespace App\Repository;

use App\Entity\Pet;
use App\PetDomain\VO\Gender;
use App\PetDomain\VO\Offset;
use App\PetDomain\VO\Slug;
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

    public function findBySearch(?string $breed, ?string $type, ?string $city, ?bool $isLookingForNewOwner = null, ?Gender $gender = null, ?Offset $offset = null): iterable
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->andWhere($queryBuilder->expr()->eq('p.isDeleted', 'false'));

        if ($gender !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.gender', ':gender'));
            $queryBuilder->setParameter('gender', $gender);
        }

        if ($isLookingForNewOwner !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.isLookingForOwner', ':isLookingForOwner'));
            $queryBuilder->setParameter('isLookingForOwner', $isLookingForNewOwner);
        }

        if (!empty($breed)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.breed', $queryBuilder->expr()->literal($breed)));
        }

        if (!empty($city)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.city', $queryBuilder->expr()->literal($city)));
        }

        if (!empty($type)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.type', $queryBuilder->expr()->literal($type)));
        }

        $queryBuilder->join('p.media', 'pm');

        return $queryBuilder->orderBy('p.updatedAt', 'DESC')
            ->setFirstResult($offset->get())
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
            ->andWhere($queryBuilder->expr()->neq('pet.city', "'undefined'"))
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(Slug $slug): ?Pet
    {
        return $this->findOneBy([
            'slug' => $slug,
            'isDeleted' => false,
        ]);
    }
}
