<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Event\AfterLogin;
use Yii\Extension\User\Form\FormLogin;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogin;
use Yiisoft\Yii\View\ViewRenderer;

final class Login
{
    public function run(
        AfterLogin $afterLogin,
        EventDispatcherInterface $eventDispatcher,
        FormLogin $FormLogin,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceLogin $serviceLogin,
        ServiceUrl $serviceUrl,
        ViewRenderer $viewRenderer
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

            return $serviceUrl->run('site/index');
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render(
                'auth/login',
                [
                    'body' => $body,
                    'data' => $FormLogin,
                ],
            );
    }
}
