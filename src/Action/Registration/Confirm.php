<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Settings\ModuleSettings;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;

final class Confirm
{
    public function run(
        CurrentUser $currentUser,
        Flash $flash,
        ModuleSettings $moduleSettings,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        RequestHandlerInterface $requestHandler,
        ServerRequestInterface $serverRequest,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator
    ): ResponseInterface {
        /** @var string|null $id */
        $id = $serverRequest->getAttribute('id');

        /** @var string|null $code */
        $code = $serverRequest->getAttribute('code');

        /** @var string $ip */
        $ip = $serverRequest->getServerParams()['REMOTE_ADDR'];

        if ($id === null || ($user = $repositoryUser->findUserById($id)) === null || $code === null) {
            return $requestHandler->handle($serverRequest);
        }

        /**
         * @var Token|null $token
         * @var User $user
         */
        $token = $repositoryToken->findTokenByParams(
            $user->getId(),
            $code,
            Token::TYPE_CONFIRMATION
        );

        if ($token === null || $token->isExpired($moduleSettings->getTokenConfirmWithin())) {
            return $requestHandler->handle($serverRequest);
        }

        if (!$token->isExpired($moduleSettings->getTokenConfirmWithin())) {
            $token->delete();
            $user->updateAttributes([
                'confirmed_at' => time(),
                'ip_last_login' => $ip,
                'last_login_at' => time(),
                'unconfirmed_email' => null,
            ]);
            $currentUser->login($user);
            $message = $translator->translate('Your user has been confirmed', [], 'user');
            $flash->add(
                'success',
                [
                    'message' => $translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );
        }

        return $serviceUrl->run('home');
    }
}
