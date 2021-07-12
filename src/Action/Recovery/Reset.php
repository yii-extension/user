<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Recovery;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Form\FormReset;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Db\Exception\StaleObjectException;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Reset
{
    /**
     * @throws StaleObjectException|Throwable
     */
    public function run(
        Flash $flash,
        FormReset $formReset,
        ModuleSettings $moduleSettings,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        RequestHandlerInterface $requestHandler,
        ServerRequestInterface $serverRequest,
        ServiceUrl $serviceUrl,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();

        /** @var string|null $id */
        $id = $serverRequest->getAttribute('id');

        /** @var string|null $code */
        $code = $serverRequest->getAttribute('code');

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
            Token::TYPE_RECOVERY
        );

        if ($token === null || $token->isExpired(0, $moduleSettings->getTokenRecoverWithin())) {
            return $requestHandler->handle($serverRequest);
        }

        if (
            $method === 'POST'
            && $formReset->load($body)
            && $validator->validate($formReset)->isValid()
            && !$token->isExpired(0, $moduleSettings->getTokenRecoverWithin())
        ) {
            $token->delete();
            $user->passwordHashUpdate($formReset->getPassword());
            $message = $translator->translate('Your password has been changed', [], 'user');
            $flash->add(
                'success',
                [
                    'message' => $translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );

            return $serviceUrl->run('login');
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('recovery/reset', ['body' => $body, 'code' => $code, 'model' => $formReset, 'id' => $id]);
    }
}
