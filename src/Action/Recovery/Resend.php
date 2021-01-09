<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Recovery;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceMailer;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Event\AfterResend;
use Yii\Extension\User\Form\FormResend;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Resend
{
    public function run(
        AfterResend $afterResend,
        Aliases $aliases,
        EventDispatcherInterface $eventDispatcher,
        FormResend $formResend,
        RepositorySetting $repositorySetting,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceMailer $serviceMailer,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();

        /** @var string $method */
        $method = $serverRequest->getMethod();

        if ($method === 'POST' && $formResend->load($body) && $formResend->validate()) {
            $email = $formResend->getEmail();

            /** @var User|null $user */
            $user = $repositoryUser->findUserByUsernameOrEmail($email);

            if ($user === null) {
                $formResend->addError(
                    'email',
                    'Thank you. If said email is registered, you will get a password reset' . '.',
                );
            }

            if ($user !== null && $user->isConfirmed()) {
                $formResend->addError('email', 'User is active.');
            }

            if ($user !== null && !$user->isConfirmed()) {
                /** @var Token $token */
                $token = $repositoryToken->findTokenById($user->getId());

                $this->sentEmail(
                    $aliases,
                    $repositorySetting,
                    $serviceMailer,
                    $token,
                    $translator,
                    $urlGenerator,
                    $user
                );

                $eventDispatcher->dispatch($afterResend);

                return $serviceUrl->run('login');
            }
        }

        if ($repositorySetting->isConfirmation()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('/recovery/resend', ['body' => $body, 'data' => $formResend]);
        }

        return $viewRenderer->withViewPath('@user-view-views')->render('site/404');
    }

    private function sentEmail(
        Aliases $aliases,
        RepositorySetting $repositorySetting,
        ServiceMailer $serviceMailer,
        Token $token,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        User $user
    ): void {
        $serviceMailer
            ->typeFlashMessageSent('warning')
            ->headerFlashMessage($translator->translate($repositorySetting->getMessageHeader()))
            ->bodyFlashMessage($translator->translate('Please check your email to activate your username'))
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
