<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\User;
use App\Entity\UserFollower;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Id;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserFollowerRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserFollowController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private UserFollowerRepositoryInterface $userFollowerRepository;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserFollowerRepositoryInterface $userFollowerRepository,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userFollowerRepository = $userFollowerRepository;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/user/{slug}/follow", methods={"POST"}, name="user_follow")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/user/{slug}/follow",
     *   tags={"User"},
     *   summary="User follows user",
     *   description="",
     *   operationId="userFollow",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "hasFollower": true,
     *           "total": 30,
     *          }
     *       }
     *    )
     * )
     */
    public function follow(string $slug, Request $request): JsonResponse
    {
        $user = $this->userRepository->findBySlug($slug);

        /** @var User $follower */
        $follower = $this->getUser();

        if ($user === null) {
            return new JsonResponse([
                'message' => 'User not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        $userFollower = $this->userFollowerRepository->getUserFollower(
            $user,
            $follower
        );

        if ($userFollower === null) {
            $userFollower = new UserFollower(
                new Id(Uuid::uuid4()->toString()),
                $user,
                $follower
            );
            $this->userFollowerRepository->save($userFollower);
            $user->addFollower($userFollower);
            $this->userRepository->save($user);
            $this->userEventRepository->log(
                new EventType(EventType::USER_FOLLOW),
                $follower,
                $this->requestUtmBuilder->build($request),
                EventContext::createByUser($user)
            );
        } else {
            $user->removeFollower($userFollower);
            $this->userFollowerRepository->remove($userFollower);
            $this->userEventRepository->log(
                new EventType(EventType::USER_UNFOLLOW),
                $follower,
                $this->requestUtmBuilder->build($request),
                EventContext::createByUser($user)
            );
        }

        return new JsonResponse(
            [
                'hasFollower' => $user->hasFollower($follower),
                'total' => $user->getCountFollowers(),
            ]
        );
    }
}
