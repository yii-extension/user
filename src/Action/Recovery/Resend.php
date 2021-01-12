<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Recovery;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Event\AfterResend;
use Yii\Extension\User\Form\FormResend;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\MailerUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Resend
{
    public function run(
        AfterResend $afterResend,
        EventDispatcherInterface $eventDispatcher,
        FormResend $formResend,
        MailerUser $mailerUser,
        RepositorySetting $repositorySetting,
        RepositoryToken $repositoryToken,
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

        $method = $serverRequest->getMethod();

        if ($method === 'POST' && $formResend->load($body) && $formResend->validate()) {
            /** @var User|null $user */
            $user = $repositoryUser->findUserByUsernameOrEmail($formResend->getEmail());

            /** @var Token $token */
            $token = $repositoryToken->findTokenById($user->getId());

            $email = $user->getEmail();
            $params = [
                'username' => $user->getUsername(),
                'url' => $urlGenerator->generateAbsolute(
                    $token->toUrl(),
                    ['id' => $token->getUserId(), 'code' => $token->getCode()]
                )
            ];

            if ($mailerUser->sendConfirmationMessage($email, $params)) {
                $serviceFlashMessage->run(
                    'success',
                    $translator->translate($repositorySetting->getMessageHeader()),
                    $translator->translate('Please check your email to activate your username'),
                );
            }

            $eventDispatcher->dispatch($afterResend);

            return $serviceUrl->run('login');
        }

        if ($repositorySetting->isConfirmation()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('/recovery/resend', ['body' => $body, 'data' => $formResend]);
        }

        return $viewRenderer->withViewPath('@user-view-views')->render('site/404');
    }
}
