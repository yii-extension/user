<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Registration;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Form\FormRegister;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\MailerUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Register
{
    public function run(
        Flash $flash,
        FormRegister $formRegister,
        MailerUser $mailerUser,
        ModuleSettings $moduleSettings,
        RepositoryUser $repositoryUser,
        RequestHandlerInterface $requestHandler,
        ServerRequestInterface $serverRequest,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $ip = (string) $serverRequest->getServerParams()['REMOTE_ADDR'];

        $formRegister->ip($ip);

        if (
            $method === 'POST' &&
            $formRegister->load($body) &&
            $validator->validate($formRegister)->isValid() &&
            $repositoryUser->register(
                $formRegister,
                $moduleSettings->isConfirmation(),
                $moduleSettings->isGeneratingPassword()
            )
        ) {
            $email = $formRegister->getEmail();
            $params = [
                'username' => $formRegister->getUsername(),
                'password' => $formRegister->getPassword(),
                'url' => $moduleSettings->isConfirmation()
                    ? $repositoryUser->generateUrlToken($urlGenerator, $moduleSettings->isConfirmation())
                    : null,
                'showPassword' => $moduleSettings->isGeneratingPassword(),
            ];

            if ($mailerUser->sendWelcomeMessage($email, $params)) {
                $message = $moduleSettings->isConfirmation()
                    ? $translator->translate('Please check your email to activate your username', [], 'user')
                    : $translator->translate('Your account has been created', [], 'user');
                $flash->add(
                    'success',
                    [
                        'message' => $translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                    ]
                );
            }

            $redirect = !$moduleSettings->isConfirmation() && !$moduleSettings->isGeneratingPassword()
                ? 'login'
                : 'home';

            return $serviceUrl->run($redirect);
        }

        if ($moduleSettings->isRegister()) {
            return $viewRenderer
                ->withViewPath('@user-view-views')
                ->render('registration/register', ['body' => $body, 'model' => $formRegister]);
        }

        return $requestHandler->handle($serverRequest);
    }
}
