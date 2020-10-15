<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\Post;

use App\Controller\Post\CreatePostController;
use App\Dto\Post\PostDto;
use App\Dto\Post\PostDtoBuilderInterface;
use App\Entity\Post;
use App\Entity\User;
use App\PetDomain\VO\Post\PostDescription;
use App\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Traits\CreateContainerTrait;

/**
 * @group unit
 * @covers \App\Controller\Post\CreatePostController
 */
final class CreatePostControllerTest extends TestCase
{
    use CreateContainerTrait;

    private const ID = 'some-id';
    private const DESCRIPTION = 'Super post!';

    private PostRepositoryInterface $postRepository;
    private PostDtoBuilderInterface $postDtoBuilder;
    private CreatePostController $createPostController;
    private User $user;

    public function testCreatePost(): void
    {
        $request = new Request();

        $postDto = new PostDto(
            self::ID,
            $this->user,
            new PostDescription(self::DESCRIPTION)
        );
        $post = new Post(
            $postDto->getId(),
            $postDto->getUser(),
            $postDto->getDescription()
        );

        $this->postDtoBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($request, $this->user)
            ->willReturn($postDto);

        $this->postRepository
            ->expects(self::atLeastOnce())
            ->method('createFromDto')
            ->with($postDto)
            ->willReturn($post);

        $result = $this->createPostController->createPost($request);
        $decodedResponse = json_decode($result->getContent(), true);
        $postResult = array_shift($decodedResponse);
        self::assertSame(self::ID, $postResult['id']);
        self::assertSame(self::DESCRIPTION, $postResult['description']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createMock(User::class);
        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->postDtoBuilder = $this->createMock(PostDtoBuilderInterface::class);
        $this->createPostController = new CreatePostController(
            $this->postRepository,
            $this->postDtoBuilder
        );

        $this->createPostController->setContainer(
            $this->createTokenContainer()
        );
    }
}
