<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Profile;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\User\Form\FormProfile;
use Yii\Extension\User\Repository\RepositoryProfile;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\User\User;
use Yiisoft\Yii\View\ViewRenderer;

final class Profile
{
    public function run(
        FormProfile $formProfile,
        RepositoryProfile $repositoryProfile,
        RepositorySetting $repositorySetting,
        ServerRequestInterface $serverRequest,
        ServiceFlashMessage $serviceFlashMessage,
        User $user,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();

        $repositoryProfile->loadData($user->getId(), $formProfile);

        if (
            $method === 'POST'
            && $formProfile->load($body)
            && $formProfile->validate()
            && $repositoryProfile->update($user->getId(), $formProfile)
        ) {
            $serviceFlashMessage->run(
                'info',
                $repositorySetting->getMessageHeader(),
                'Your data has been saved.'
            );
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('profile/profile', ['body' => $body, 'data' => $formProfile]);
    }
}
