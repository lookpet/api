<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Dto\Authentication\UserLoginDto;
use App\Dto\Authentication\UserLoginDtoBuilder;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Message\MailWelcomeMessage;
use App\PetDomain\VO\EventType;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\EmailTemplateSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthenticationController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private ValidatorInterface $validator;
    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;
    private UserLoginDtoBuilder $loginDtoBuilder;
    private MessageBusInterface $messageBus;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        UserLoginDtoBuilder $loginDtoBuilder,
        MessageBusInterface $messageBus,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->loginDtoBuilder = $loginDtoBuilder;
        $this->messageBus = $messageBus;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/authentication/login", methods={"POST"}, name="api_login")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/authentication/login",
     *   tags={"Authentication"},
     *   summary="Lets user to login",
     *   description="",
     *   operationId="login",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=UserLoginDto::class))
     *   ),
     *   @SWG\Response(response=400, description="Invalid email or password",
     *     examples={
     *      "application/json": {
     *          "message":"Invalid email or password"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          "user": {
     *              "slug": "6r7civ9rl4gf",
     *              "firstName": "Svetoslav",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": null
     *           },
     *           "token": "bd76fbd73fe552c0600437d83aa56b0c0b78eb22117b8582fecb9daa6fd5e308d994f64e05aa0538b49c07acd10ac27630c82a031a985e60053e5ec1",
     *           "expires_at": {
     *              "date": "2020-05-25 16:23:41.290766",
     *              "timezone_type": 3,
     *              "timezone": "Europe/London"
     *              }
     *           }
     *       }
     *    )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $userLoginDto = $this->loginDtoBuilder->build($request);
            $user = $this->userRepository->findByEmail($userLoginDto->getEmail());

            if ($user === null) {
                return new JsonResponse([
                    'message' => 'Invalid email or password',
                ], 400);
            }

            if (!$this->passwordEncoder->isPasswordValid(
                $user,
                $userLoginDto->getPassword()
            )) {
                return new JsonResponse([
                    'message' => 'Invalid email or password',
                ], 400);
            }

            if (!$user->hasActiveApiToken()) {
                $apiToken = new ApiToken($user);
                $user->addApiToken(
                    $apiToken
                );
                $this->entityManager->persist($apiToken);
                $this->entityManager->flush();
            }

            $this->userEventRepository->log(
                new EventType(EventType::LOGIN),
                $user,
                $this->requestUtmBuilder->build($request)
            );

            return $this->json([
                'user' => $user,
                'token' => $user->getActiveApiToken()->getToken(),
                'expires_at' => $user->getActiveApiToken()->getExpiresAt(),
            ]);
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        }
    }

    /**
     * @Route("/api/v1/authentication/register", methods={"POST"}, name="api_register")
     *
     * @param Request $request
     * @param EmailTemplateSenderInterface $emailTemplateSender
     *
     * @return JsonResponse
     *
     * @SWG\Post(path="/api/v1/authentication/register",
     *   tags={"Authentication"},
     *   summary="Lets user to create new account",
     *   description="",
     *   operationId="register",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Lets login and get token",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=UserLoginDto::class))
     *   ),
     *   @SWG\Response(response=400, description="User already exist; empty email or password",
     *     examples={
     *      "application/json": {
     *          "message":"User already exist"
     *      }
     *    }
     *  ),
     *   @SWG\Response(
     *     response=200,
     *     description="OK",
     *     examples={
     *     "application/json": {
     *          "user": {
     *              "slug": "6r7civ9rl4gf",
     *              "firstName": "Svetoslav",
     *              "phone": null,
     *              "description": null,
     *              "city": null,
     *              "avatar": null
     *           },
     *           "token": "bd76fbd73fe552c0600437d83aa56b0c0b78eb22117b8582fecb9daa6fd5e308d994f64e05aa0538b49c07acd10ac27630c82a031a985e60053e5ec1",
     *           "expires_at": "2020-05-25T16:23:41+01:00"
     *           }
     *       }
     *    )
     * )
     */
    public function register(
        Request $request
    ): JsonResponse {
        try {
            $userLoginDto = $this->loginDtoBuilder->build($request);

            $user = new User(
                $userLoginDto->getId(),
                $userLoginDto->getSlug(),
                $userLoginDto->getFirstName()
            );

            $user->setEmail(
                $userLoginDto->getEmail()
            );

            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $userLoginDto->getPassword()
                )
            );

            $userExist = $this->userRepository->findByEmail(
                $userLoginDto->getEmail()
            );

            if ($userExist !== null) {
                return new JsonResponse([
                    'message' => 'User already exist',
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->entityManager->persist($user);
            $apiToken = new ApiToken($user);
            $this->entityManager->persist($apiToken);
            $this->entityManager->flush();

            $this->messageBus->dispatch(
                new MailWelcomeMessage($user->getUuid())
            );

            $this->userEventRepository->log(
                new EventType(EventType::REGISTRATION),
                $user,
                $this->requestUtmBuilder->build($request)
            );

            return new JsonResponse(
                [
                    'user' => $user,
                    'token' => $apiToken->getToken(),
                    'expires_at' => $apiToken->getExpiresAt(),
                ]
            );
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
