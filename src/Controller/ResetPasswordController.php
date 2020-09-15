<?php

namespace App\Controller;

use App\EmailTemplates\EmailTemplateDto;
use App\PetDomain\VO\EmailRecipient;
use App\Repository\UserRepositoryInterface;
use App\Service\EmailTemplateSenderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @var EmailTemplateSenderInterface
     */
    private EmailTemplateSenderInterface $emailTemplateSender;
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    public function __construct(EmailTemplateSenderInterface $emailTemplateSender, UserRepositoryInterface $userRepository)
    {
        $this->emailTemplateSender = $emailTemplateSender;
        $this->userRepository = $userRepository;
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

        $this->emailTemplateSender->send(
            (new EmailTemplateDto(
                EmailRecipient::create(
                    $user->getEmail(),
                    $user->getName()
                ),
                'Восстановить доступ к look.pet',
                $_ENV['MJ_TEMPLATE_RESET_PASSWORD']
            ))->setVariables([
                'password_reset_link' => 'https://look.pet',
            ]));

        return new JsonResponse([], Response::HTTP_OK);
    }
}
