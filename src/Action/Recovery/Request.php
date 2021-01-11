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
use Yii\Extension\User\Event\AfterRequest;
use Yii\Extension\User\Form\FormRequest;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\MailerUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Request
{
    public function run(
        AfterRequest $afterRequest,
        EventDispatcherInterface $eventDispatcher,
        FormRequest $formRequest,
        MailerUser $mailerUser,
        RepositorySetting $repositorySetting,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        ServiceUrl $serviceUrl,
        Token $token,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();

        $method = $serverRequest->getMethod();

        if ($method === 'POST' && $formRequest->load($body) && $formRequest->validate()) {
            $email = $formRequest->getEmail();

            /** @var User|null $user */
            $user = $repositoryUser->findUserByUsernameOrEmail($email);

            if ($user === null) {
                $formRequest->addError('email', 'Email not registered');
            }

            if ($user !== null && !$user->isConfirmed()) {
                $formRequest->addError('email', 'Inactive user');
            }

            if ($user !== null && $user->isConfirmed()) {
                $token->deleteAll(['user_id' => $user->getId(), 'type' => Token::TYPE_RECOVERY]);

                $repositoryToken->register($user->getId(), Token::TYPE_RECOVERY);

                /** @var Token $token */
                $token = $repositoryToken->findTokenById($user->getId());

                $email = $user->getEmail();
                $params = [
                    'username' => $user->getUsername(),
                    'url' => $urlGenerator->generateAbsolute(
                        $token->toUrl(),
                        ['id' => $token->getAttribute('user_id'), 'code' => $token->getAttribute('code')]
                    )
                ];

                if ($mailerUser->sendRecoveryMessage($email, $params)) {
                    $serviceFlashMessage->run(
                        'success',
                        $translator->translate($repositorySetting->getMessageHeader()),
                        $translator->translate('Please check your email to change your password'),
                    );
                }

                $eventDispatcher->dispatch($afterRequest);

                return $serviceUrl->run('login');
            }
        }

        if ($repositorySetting->isPasswordRecovery()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('/recovery/request', ['body' => $body, 'data' => $formRequest]);
        }

        return $viewRenderer->withViewPath('@user-view-views')->render('site/404');
    }
}
