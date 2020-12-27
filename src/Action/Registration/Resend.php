<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceView;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\Service\ServiceMailer;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\Event\AfterResend;
use Yii\Extension\User\Form\FormResend;
use Yii\Extension\User\Settings\RepositorySetting;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\UrlGeneratorInterface;

final class Resend
{
    public function run(
        AfterResend $afterResend,
        Aliases $aliases,
        EventDispatcherInterface $eventDispatcher,
        FormResend $formResend,
        ServerRequestInterface $serverRequest,
        RepositorySetting $repositorySetting,
        UrlGeneratorInterface $urlGenerator,
        RepositoryUser $repositoryUser,
        ServiceView $serviceView,
        ServiceUrl $serviceUrl,
        RepositoryToken $repositoryToken,
        ServiceMailer $serviceMailer,
        Token $token
    ): ResponseInterface {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();

        if ($method === 'POST' && $formResend->load($body) && $formResend->validate()) {
            $email = $formResend->getAttributeValue('email');
            $user = $repositoryUser->findUserByUsernameOrEmail($email);

            if ($user === null) {
                $formResend->addError(
                    'email',
                    'Thank you. If said email is registered, you will get a password reset.',
                );
            }

            /** @var User $user */
            if ($user !== null && $user->isConfirmed()) {
                $formResend->addError('email', 'User is active.');
            }

            if ($user !== null && !$user->isConfirmed()) {
                /** @var Token $token */
                $token = $repositoryToken->findTokenById($user->getId());

                $this->sentEmail($aliases, $repositorySetting, $serviceMailer, $token, $urlGenerator, $user);

                $eventDispatcher->dispatch($afterResend);

                return $serviceUrl->run('index');
            }
        }

        if ($repositorySetting->isConfirmation()) {
            return $serviceView
                ->viewPath('@user-view-views')
                ->render(
                    '/registration/resend',
                    [
                        'action' => $urlGenerator->generate('resend'),
                        'body' => $body,
                        'data' => $formResend,
                        'settings' => $repositorySetting,
                        'urlGenerator' => $urlGenerator,
                    ]
                );
        }

        return $serviceView->viewPath('@user-view-views')->render('site/404');
    }

    private function sentEmail(
        Aliases $aliases,
        RepositorySetting $repositorySetting,
        ServiceMailer $serviceMailer,
        Token $token,
        UrlGeneratorInterface $urlGenerator,
        User $user
    ): void {
        $serviceMailer
            ->typeFlashMessageSent('warning')
            ->bodyFlashMessage('Please check your email to activate your username.')
            ->run(
                $repositorySetting->getEmailFrom(),
                $user->getEmail(),
                $repositorySetting->getSubjectConfirm(),
                $aliases->get('@user-view-mail'),
                ['html' => 'confirmation', 'text' => 'text/confirmation'],
                [
                    'username' => $user->getUsername(),
                    'url' => $urlGenerator->generateAbsolute(
                        $token->toUrl(),
                        ['id' => $token->getAttribute('user_id'), 'code' => $token->getAttribute('code')]
                    )
                ]
            );
    }
}
