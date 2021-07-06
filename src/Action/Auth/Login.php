<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Form\FormLogin;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Login
{
    public function run(
        Flash $flash,
        FormLogin $formLogin,
        ServerRequestInterface $serverRequest,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $ip = (string) $serverRequest->getServerParams()['REMOTE_ADDR'];

        $formLogin->ip($ip);

        if ($method === 'POST' && $formLogin->load($body) && $validator->validate($formLogin)->isValid()) {
            $lastLogin = $formLogin->getLastLogout() > 0
                ? date('Y-m-d G:i:s', $formLogin->getLastLogout())
                : $translator->translate('This is your first login - Welcome', [], 'user');
            $message = $translator->translate('Sign in successful - {lastLogin}', ['lastLogin' => $lastLogin], 'user');
            $flash->add(
                'success',
                [
                    'message' => $translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );

            return $serviceUrl->run('home');
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('auth/login', ['body' => $body, 'data' => $formLogin]);
    }
}
