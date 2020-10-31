<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\Post\PostDtoBuilderInterface;
use App\Repository\PostRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreatePostController extends AbstractController
{
    private PostRepositoryInterface $postRepository;
    private PostDtoBuilderInterface $postDtoBuilder;

    public function __construct(
        PostRepositoryInterface $postRepository,
        PostDtoBuilderInterface $postDtoBuilder
    ) {
        $this->postRepository = $postRepository;
        $this->postDtoBuilder = $postDtoBuilder;
    }

    /**
     * @Route("/api/v1/post", methods={"POST"}, name="create_post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createPost(Request $request): JsonResponse
    {
        $post = $this->postRepository->createFromDto(
            $this->postDtoBuilder->build($request, $this->getUser())
        );

        return new JsonResponse([
            $post,
        ], Response::HTTP_OK);
    }
}
