<?php

namespace App\Repository;

use App\Dto\Post\PostDto;
use App\Entity\Post;
use App\Entity\User;
use App\PetDomain\VO\Offset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
        $this->entityManager = $registry->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function createFromDto(PostDto $postDto): Post
    {
        $post = new Post(
            $postDto->getId(),
            $postDto->getUser(),
            $postDto->getDescription(),
            ...$postDto->getMedia()
        );
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeed(Offset $offset, ?User $user = null): iterable
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder->join('p.media', 'pm')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($offset->get())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
