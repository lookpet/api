<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\Entity\ApiToken;
use App\Entity\User;
use App\PetDomain\VO\EventType;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\Notification\WelcomeEmailNotifier;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaceBookController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private Slugify $slugify;
    private WelcomeEmailNotifier $welcomeEmailNotifier;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        Slugify $slugify,
        WelcomeEmailNotifier $welcomeEmailNotifier,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->userRepository = $userRepository;
        $this->slugify = $slugify;
        $this->welcomeEmailNotifier = $welcomeEmailNotifier;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/authentication/facebook", methods={"POST"}, name="public_authentication_facebook")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function facebook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->request->has('profile')) {
            $profile = $request->request->get('profile');
            $user = null;

            if (!empty($profile['email'])) {
                $user = $this->userRepository->findOneBy([
                    'email' => $profile['email'],
                ]);
            }

            if ($user === null) {
                $user = $this->userRepository->findOneBy([
                    'provider' => 'facebook',
                    'providerId' => $profile['id'],
                ]);
            }

            if ($user === null) {
                $user = new User();
                $user->setEmail($profile['email'] ?? null);
                $user->setName($profile['name']);
                $user->setFirstName($profile['first_name']);
                $user->setLastName($profile['last_name']);
                $user->setProvider('facebook');
                $user->setProviderId($profile['id']);
                $user->setSlug(
                    $this->slugify->slugify(
                        $profile['name'],
                        random_int(1000, 99999999)
                    ),
                );
                $this->welcomeEmailNotifier->notify($user);
                $this->userRepository->updateNotificationDate($user);
                $this->userEventRepository->log(
                    new EventType(EventType::FACEBOOK_REGISTRATION),
                    $user,
                    $this->requestUtmBuilder->build($request)
                );
            } else {
                $this->userEventRepository->log(
                    new EventType(EventType::FACEBOOK_LOGIN),
                    $user,
                    $this->requestUtmBuilder->build($request)
                );
            }

            $user->setProviderLastResponse($request->getContent());
            $entityManager->persist($user);
            $entityManager->flush();

            $activeToken = $user->getActiveApiToken();

            if ($activeToken === null) {
                $activeToken = new ApiToken($user);
                $entityManager->persist($activeToken);
                $entityManager->flush();
            }

            return $this->json([
                'user' => $user,
                'token' => $activeToken->getToken(),
                'expires_at' => $activeToken->getExpiresAt(),
            ]);
        }

        return new JsonResponse([], Response::HTTP_FORBIDDEN);
    }
}
