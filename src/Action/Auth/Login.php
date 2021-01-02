<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Event\AfterLogin;
use Yii\Extension\User\Form\FormLogin;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogin;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Yii\View\ViewRenderer;

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
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();

        /** @var string $method */
        $method = $serverRequest->getMethod();

        /** @var string $ip */
        $ip = $serverRequest->getServerParams()['REMOTE_ADDR'];

        if (
            $method === 'POST'
            && $FormLogin->load($body)
            && $FormLogin->validate()
            && $serviceLogin->run($repositoryUser, $ip)
        ) {
            $eventDispatcher->dispatch($afterLogin);

            $serviceFlashMessage->run(
                'success',
                $repositorySetting->getMessageHeader(),
                'Sign in successful - ' . date("F j, Y, g:i a")
            );

            return $serviceUrl->run('site/index');
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('auth/login', ['body' => $body, 'data' => $FormLogin]);
    }
}
