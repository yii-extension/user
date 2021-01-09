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
use Yii\Extension\User\Event\AfterRequest;
use Yii\Extension\User\Form\FormRequest;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Request
{
    public function run(
        AfterRequest $afterRequest,
        Aliases $aliases,
        EventDispatcherInterface $eventDispatcher,
        FormRequest $formRequest,
        RepositorySetting $repositorySetting,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceMailer $serviceMailer,
        ServiceUrl $serviceUrl,
        Token $token,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();

        /** @var string $method */
        $method = $serverRequest->getMethod();

        if ($method === 'POST' && $formRequest->load($body) && $formRequest->validate()) {
            $email = $formRequest->getEmail();

            /** @var User|null $user */
            $user = $repositoryUser->findUserByUsernameOrEmail($email);

            if ($user === null) {
                $formRequest->addError('email', 'Email not registered.');
            }

            if ($user !== null && !$user->isConfirmed()) {
                $formRequest->addError('email', 'Inactive user.');
            }

            if ($user !== null && $user->isConfirmed()) {
                $token->deleteAll(['user_id' => $user->getId(), 'type' => Token::TYPE_RECOVERY]);

                $repositoryToken->register($user->getId(), Token::TYPE_RECOVERY);

                /** @var Token $token */
                $token = $repositoryToken->findTokenById($user->getId());

                $this->sentEmail(
                    $aliases,
                    $repositorySetting,
                    $serviceMailer,
                    $token,
                    $translator,
                    $urlGenerator,
                    $user,
                );

                $eventDispatcher->dispatch($afterRequest);

                return $serviceUrl->run('site/index');
            }
        }

        if ($repositorySetting->isPasswordRecovery()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('/recovery/request', ['body' => $body, 'data' => $formRequest]);
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
            ->typeFlashMessageSent('info')
            ->headerFlashMessage($translator->translate($repositorySetting->getMessageHeader()))
            ->bodyFlashMessage($translator->translate('Please check your email to change your password'))
            ->run(
                $repositorySetting->getEmailFrom(),
                $user->getEmail(),
                $repositorySetting->getSubjectRecovery(),
                $aliases->get('@user-view-mail'),
                ['html' => 'recovery', 'text' => 'text/recovery'],
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
