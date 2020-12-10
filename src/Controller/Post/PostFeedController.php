<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\PetDomain\VO\Limit;
use App\PetDomain\VO\Offset;
use App\PetDomain\VO\PageNumber;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\PostResponseBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class PostFeedController extends AbstractController
{
    private PostRepositoryInterface $postRepository;
    private PostResponseBuilderInterface $postResponseBuilder;

    public function __construct(
        PostRepositoryInterface $postRepository,
        PostResponseBuilderInterface $postResponseBuilder
    ) {
        $this->postRepository = $postRepository;
        $this->postResponseBuilder = $postResponseBuilder;
    }

    /**
     * @Route("/api/v1/feed", methods={"GET"}, name="public_feed")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function feed(Request $request): JsonResponse
    {
        $posts = $this->postRepository->getFeed(
            Offset::create(
                PageNumber::create(
                    (int) $request->get('page')
                ),
                Limit::create()
            )
        );

        return $this->postResponseBuilder->build($this->getUser(), ...$posts);
    }
}
