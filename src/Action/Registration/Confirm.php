<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\Service\ServiceView;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\Event\AfterConfirm;
use Yii\Extension\User\Settings\RepositorySetting;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogin;

final class Confirm
{
    public function run(
        AfterConfirm $afterConfirm,
        EventDispatcherInterface $eventDispatcher,
        ServiceLogin $serviceLogin,
        ServerRequestInterface $serverRequest,
        RepositorySetting $repositorySetting,
        Repositorytoken $repositoryToken,
        RepositoryUser $repositoryUser,
        ServiceUrl $serviceUrl,
        ServiceView $serviceView
    ): ResponseInterface {
        $id = $serverRequest->getAttribute('id');
        $code = $serverRequest->getAttribute('code');
        $ip = $serverRequest->getServerParams()['REMOTE_ADDR'];

        if ($id === null || ($user = $repositoryUser->findUserById($id)) === null || $code === null) {
            return $serviceView->viewPath('@user-view')->render('404');
        }

        /**
         * @var Token $token
         * @var User $user
         */
        $token = $repositoryToken->findTokenByParams(
            (int) $user->getId(),
            $code,
            Token::TYPE_CONFIRMATION
        );

        if ($token === null || $token->isExpired($repositorySetting->getTokenConfirmWithin())) {
            return $serviceView->viewPath('@user-view')->render('404');
        }

        if (
            $serviceLogin->isLoginConfirm($user, $ip)
            && !$token->isExpired($repositorySetting->getTokenConfirmWithin())
        ) {
            $token->delete();

            $user->updateAttributes([
                'unconfirmed_email' => null,
                'confirmed_at' => time()
            ]);

            $eventDispatcher->dispatch($afterConfirm);

            return $serviceUrl->run('index');
        }

        return $webController
            ->withFlash(
                'is-danger',
                $repositorySetting->getMessageHeader(),
                'Your username could not be confirmed.'
            )
            ->redirectResponse('admin/index');
    }
}
