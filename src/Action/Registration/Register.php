<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Event\AfterRegister;
use Yii\Extension\User\Form\FormRegister;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\MailerUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Register
{
    public function run(
        AfterRegister $afterRegister,
        EventDispatcherInterface $eventDispatcher,
        FormRegister $formRegister,
        MailerUser $mailerUser,
        RepositorySetting $repositorySetting,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();

        /** @var string $method */
        $method = $serverRequest->getMethod();

        $formRegister->ip($serverRequest->getServerParams()['REMOTE_ADDR']);

        if (
            $method === 'POST'
            && $formRegister->load($body)
            && $formRegister->validate()
            && $repositoryUser->register(
                $formRegister,
                $repositorySetting->isConfirmation(),
                $repositorySetting->isGeneratingPassword()
            )
        ) {
            $email = $formRegister->getEmail();
            $params = [
                'username' => $formRegister->getUsername(),
                'password' => $formRegister->getPassword(),
                'url' => $repositorySetting->isConfirmation()
                    ? $repositoryUser->generateUrlToken($urlGenerator, $repositorySetting->isConfirmation())
                    : null,
                'showPassword' => $repositorySetting->isGeneratingPassword()
            ];

            if ($mailerUser->sendWelcomeMessage($email, $params)) {
                $bodyMessage = $repositorySetting->isConfirmation()
                    ? $translator->translate('Please check your email to activate your username', [], 'user')
                    : $translator->translate('Your account has been created', [], 'user');

                $serviceFlashMessage->run(
                    'success',
                    $translator->translate('System Notification', [], 'user'),
                    $bodyMessage,
                );
            }

            $eventDispatcher->dispatch($afterRegister);

            $redirect = !$repositorySetting->isConfirmation() && !$repositorySetting->isGeneratingPassword()
                ? 'login'
                : 'site/index';
            return $serviceUrl->run($redirect);
        }

        if ($repositorySetting->isRegister()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('/registration/register', ['body' => $body, 'data' => $formRegister]);
        }

        return $viewRenderer->withViewPath('@user-view-views')->render('site/404');
    }
}
