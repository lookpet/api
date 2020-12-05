<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\User;
use App\Entity\UserMessage;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Message;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserMessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserMessageController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private UserMessageRepositoryInterface $userMessageRepository;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserMessageRepositoryInterface $userMessageRepository,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userMessageRepository = $userMessageRepository;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/user/{slug}/chat", methods={"POST"}, name="user_chat")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/user/{slug}/chat",
     *   tags={"User"},
     *   summary="Send message to user",
     *   description="",
     *   operationId="userChat",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "message": "Hello!"
     *          }
     *       }
     *    )
     * )
     */
    public function chat(string $slug, Request $request): JsonResponse
    {
        if (!$request->request->has('message') || empty($request->request->get('message'))) {
            return new JsonResponse([
                'message' => 'Message cannot be empty',
            ], Response::HTTP_BAD_REQUEST);
        }

        $toUser = $this->userRepository->findBySlug($slug);

        if ($toUser === null) {
            return new JsonResponse([
                'message' => 'User not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $fromUser */
        $fromUser = $this->getUser();

        $userMessage = new UserMessage(
            Id::create(Uuid::uuid4()->toString()),
            $fromUser,
            $toUser,
            Message::create($request->request->get('message'))
        );
        $this->userMessageRepository->save($userMessage);
        $this->userEventRepository->log(
            new EventType(EventType::SEND_MESSAGE),
            $fromUser,
            $this->requestUtmBuilder->build($request),
            EventContext::createByUser($toUser)
        );

        return new JsonResponse([
            $userMessage,
        ]);
    }

    /**
     * @Route("/api/v1/user/{slug}/chat", methods={"GET"}, name="get_user_chat")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Get(path="/api/v1/user/{slug}/chat",
     *   tags={"User"},
     *   summary="Get chat messages",
     *   description="",
     *   operationId="getUserChat",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *           "message": "Hello!"
     *          }
     *       }
     *    )
     * )
     */
    public function chatMessages(string $slug): JsonResponse
    {
        $toUser = $this->userRepository->findBySlug($slug);

        if ($toUser === null) {
            return new JsonResponse([
                'message' => 'User not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $this->userMessageRepository->getChatMessages(
                $this->getUser(),
                $toUser
            )
        );
    }

    /**
     * @Route("/api/v1/user/chat/list", methods={"GET"}, name="get_user_chat_list")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function chatList(): JsonResponse
    {
        return new JsonResponse(
            $this->userMessageRepository->getChatLastMessages(
                $this->getUser()
            )
        );
    }

    public function readMessages(string $slug): JsonResponse
    {
        $toUser = $this->userRepository->findBySlug($slug);

        if ($toUser === null) {
            return new JsonResponse([
                'message' => 'User not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([]);
    }
}
