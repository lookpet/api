<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Dto\Post\PostDto;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 * @covers \App\Repository\PostRepository
 */
class PostRepositoryTest extends TestCase
{
    private ObjectManager $entityManager;
    private ManagerRegistry $managerRegistry;
    private PostRepository $postRepository;

    public function testItCreateFromDto(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $user = $this->createMock(User::class);
        $postDto = new PostDto($uuid, $user);
        $post = new Post(
            $postDto->getId(),
            $postDto->getUser(),
            $postDto->getDescription()
        );
        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($post);

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $postResult = $this->postRepository->createFromDto($postDto);
        self::assertSame($post->getId(), $postResult->getId());
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry
            ->expects(self::atLeastOnce())
            ->method('getManager')
            ->willReturn($this->entityManager);

        $this->managerRegistry
            ->expects(self::atLeastOnce())
            ->method('getManagerForClass')
            ->with(Post::class)
            ->willReturn($this->entityManager);

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('getClassMetadata')
            ->with(Post::class)
            ->willReturn($this->createMock(ClassMetadata::class));

        $this->postRepository = new PostRepository(
            $this->managerRegistry
        );

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->postRepository);
    }
}
