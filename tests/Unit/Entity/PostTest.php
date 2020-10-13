<?php

namespace Tests\Unit\Entity;

use App\Entity\Media;
use App\Entity\Post;
use App\Entity\User;
use App\PetDomain\VO\Post\PostDescription;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 */
final class PostTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $user = $this->createMock(User::class);
        $media = $this->createMock(Media::class);
        $post = new Post(
            $uuid,
            $user,
            new PostDescription('test'),
            ...[$media]
        );
        self::assertSame($uuid, $post->getId());
        self::assertSame($user, $post->getUser());
        self::assertCount(1, $post->getMedia());
    }
}
