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
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Login
{
    public function run(
        AfterLogin $afterLogin,
        EventDispatcherInterface $eventDispatcher,
        FormLogin $formLogin,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $formLogin->ip($serverRequest->getServerParams()['REMOTE_ADDR']);

        if ($method === 'POST' && $formLogin->load($body) && $formLogin->validate()) {
            $eventDispatcher->dispatch($afterLogin);

            $bodyMessage = $translator->translate('Sign in successful - you are welcome', [], 'user-flash-message');

            if ($formLogin->getLastLogout() > 0) {
                $bodyMessage = $translator->translate(
                    'Sign in successful - {date}',
                    ['date' =>  date('Y-m-d G:i:s', $formLogin->getLastLogout())],
                    'user-flash-message',
                );
            }

            $serviceFlashMessage->run(
                'success',
                $translator->translate('System Notification', [], 'user-flash-message'),
                $bodyMessage,
            );

            return $serviceUrl->run('home');
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('auth/login', ['body' => $body, 'data' => $formLogin]);
    }
}
