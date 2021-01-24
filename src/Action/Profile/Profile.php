<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Profile;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\User\Form\FormProfile;
use Yii\Extension\User\Repository\RepositoryProfile;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\User;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Profile
{
    public function run(
        FormProfile $formProfile,
        RepositoryProfile $repositoryProfile,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        TranslatorInterface $translator,
        User $user,
        ValidatorInterface $validator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();

        $repositoryProfile->loadData($user->getId(), $formProfile);

        if (
            $method === 'POST'
            && $formProfile->load($body)
            && $formProfile->validate($validator)
            && $repositoryProfile->update($user->getId(), $formProfile)
        ) {
            $serviceFlashMessage->run(
                'success',
                $translator->translate('System Notification', [], 'user'),
                $translator->translate('Your data has been saved', [], 'user'),
            );
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('profile/profile', ['body' => $body, 'data' => $formProfile]);
    }
}
