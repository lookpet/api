<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private const PUBLIC_ROUTES = [
        'api_login',
        'api_register',
        'public_pet_slug',
        'user_pets',
        'public_pet_pets',
        'search_pet',
        'public_users',
        'public_pet_types',
        'public_dog_breeds',
        'public_cat_breeds',
        'api.swagger_ui',
        'public_get_user',
        'public_genders',
    ];
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function supports(Request $request): bool
    {
        return !in_array($request->attributes->get('_route'), self::PUBLIC_ROUTES, true);
    }

    public function getCredentials(Request $request): string
    {
        if (!$request->headers->has('Authorization')
            || 0 !== mb_strpos($request->headers->get('Authorization'), 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('Invalid API token');
        }

        $authorizationHeader = $request->headers->get('Authorization');

        return mb_substr($authorizationHeader, 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = $this->apiTokenRepository->findOneBy([
            'token' => $credentials,
        ]);

        if ($token === null || $token->isExpired()) {
            throw new CustomUserMessageAuthenticationException('Invalid API token');
        }

        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessageKey(),
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void
    {
        //allows the request to continue
    }

    public function start(Request $request, AuthenticationException $authException = null): void
    {
        throw new \Exception('Not support by current authenticator');
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
