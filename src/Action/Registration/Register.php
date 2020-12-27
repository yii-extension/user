<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\ServiceMailer;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\Service\ServiceView;
use Yii\Extension\User\Event\AfterRegister;
use Yii\Extension\User\Form\FormRegister;
use Yii\Extension\User\Settings\RepositorySetting;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\UrlGeneratorInterface;

final class Register
{
    public function run(
        AfterRegister $afterRegister,
        Aliases $aliases,
        EventDispatcherInterface $eventDispatcher,
        FormRegister $formRegister,
        RepositorySetting $repositorySetting,
        RepositoryUser $repositoryUser,
        ServerRequestInterface $serverRequest,
        UrlGeneratorInterface $urlGenerator,
        ServiceFlashMessage $serviceFlashMessage,
        ServiceMailer $serviceMailer,
        ServiceUrl $serviceUrl,
        ServiceView $serviceView
    ): ResponseInterface {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $formRegister->ip($serverRequest->getServerParams()['REMOTE_ADDR']);

        if (
            $method === 'POST'
            && $formRegister->load($body)
            && $formRegister->validate()
            && $repositoryUser->register(
                $formRegister,
                $repositorySetting->isConfirmation(),
                $repositorySetting->isGeneratingPassword()
            )
        ) {
            $serviceMailer
                ->bodyFlashMessage(
                    $repositorySetting->isConfirmation()
                        ? 'Please check your email to activate your username.'
                        : 'Your account has been created.',
                )
                ->run(
                    $repositorySetting->getEmailFrom(),
                    $formRegister->getEmail(),
                    $repositorySetting->getSubjectWelcome(),
                    $aliases->get('@user-view-mail'),
                    ['html' => 'welcome', 'text' => 'text/welcome'],
                    [
                        'username' => $formRegister->getUsername(),
                        'password' => $formRegister->getPassword(),
                        'url' => $repositorySetting->isConfirmation()
                            ? $repositoryUser->generateUrlToken($urlGenerator, $repositorySetting->isConfirmation())
                            : null,
                        'showPassword' => $repositorySetting->isGeneratingPassword()
                    ]
                );

            $eventDispatcher->dispatch($afterRegister);

            return $serviceUrl->run('index');
        }

        if ($repositorySetting->isRegister()) {
            return $serviceView
                ->viewPath('@user-view-views')
                ->render(
                    '/registration/register',
                    [
                        'action' => $urlGenerator->generate('register'),
                        'body' => $body,
                        'data' => $formRegister,
                        'settings' => $repositorySetting,
                        'url' => $urlGenerator,
                    ]
                );
        }

        return $serviceView->viewPath('@user-view-views')->render('site/404');
    }
}
