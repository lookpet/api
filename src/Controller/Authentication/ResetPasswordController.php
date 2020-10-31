<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Dto\Event\RequestUtmBuilderInterface;
use App\EmailTemplates\EmailTemplateDto;
use App\PetDomain\VO\EmailRecipient;
use App\PetDomain\VO\EventType;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\EmailTemplateSenderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private EmailTemplateSenderInterface $emailTemplateSender;
    private UserRepositoryInterface $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EmailTemplateSenderInterface $emailTemplateSender,
        UserRepositoryInterface $userRepository,
        RequestUtmBuilderInterface $requestUtmBuilder,
        UserEventRepositoryInterface $userEventRepository
    ) {
        $this->emailTemplateSender = $emailTemplateSender;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->requestUtmBuilder = $requestUtmBuilder;
        $this->userEventRepository = $userEventRepository;
    }

    /**
     * @Route("/api/v1/authentication/password/reset", methods={"POST"}, name="api_password_reset")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sendResetPasswordEmail(Request $request): JsonResponse
    {
        $email = $request->request->get('email');

        if ($email === null) {
            return new JsonResponse([
                'message' => 'Empty email',
            ], Response::HTTP_FORBIDDEN);
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        $password = $this->randomPassword();
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $this->emailTemplateSender->send(
            (new EmailTemplateDto(
                EmailRecipient::create(
                    $user->getEmail(),
                    $user->getName()
                ),
                'Восстановить доступ к look.pet',
                (int) $_ENV['MJ_TEMPLATE_RESET_PASSWORD']
            ))->setVariables([
                'new_password' => $password,
            ]));

        $this->userEventRepository->log(
            new EventType(EventType::RESET_PASSWORD),
            $user,
            $this->requestUtmBuilder->build($request)
        );

        return new JsonResponse([], Response::HTTP_OK);
    }

    private function randomPassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';

        return mb_substr(str_shuffle($chars), 0, $length);
    }
}
