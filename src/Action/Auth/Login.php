<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\Service\ServiceView;
use Yii\Extension\User\Event\AfterLogin;
use Yii\Extension\User\Form\FormLogin;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogin;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Router\UrlGeneratorInterface;

final class Login
{
    public function run(
        AfterLogin $afterLogin,
        EventDispatcherInterface $eventDispatcher,
        FormLogin $FormLogin,
        RepositorySetting $repositorySetting,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        ServiceLogin $serviceLogin,
        ServiceUrl $serviceUrl,
        ServiceView $serviceView,
        UrlGeneratorInterface $urlGenerator
    ): ResponseInterface {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $ip = $serverRequest->getServerParams()['REMOTE_ADDR'];

        if (
            $method === 'POST'
            && $FormLogin->load($body)
            && $FormLogin->validate()
            && $serviceLogin->run($repositoryUser, $ip)
        ) {
            $eventDispatcher->dispatch($afterLogin);

            return $serviceUrl->run('index');
        }

        return $serviceView
            ->viewPath('@user-view-views')
            ->render(
                'auth/login',
                [
                    'action' => $urlGenerator->generate('login'),
                    'body' => $body,
                    'data' => $FormLogin,
                    'isPasswordRecovery' => $repositorySetting->isPasswordRecovery(),
                    'linkResend' => $urlGenerator->generate('resend'),
                ]
            );
    }
}
