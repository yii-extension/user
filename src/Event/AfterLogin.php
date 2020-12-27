<?php

declare(strict_types=1);

namespace Yii\Extension\User\Event;

use Yii\Extension\User\Settings\RepositorySetting;
use Yii\Extension\Service\ServiceFlashMessage;
use Yiisoft\User\User;

final class AfterLogin
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    public function addFlash(
        RepositorySetting $repositorySetting,
        ServiceFlashMessage $serviceFlashMessage,
        User $identity
    ): void {
        $serviceFlashMessage->run(
            'success',
            $repositorySetting->getMessageHeader(),
            'Sign in successful - ' . $identity->getIdentity()->getUsername() . ' - ' . date("F j, Y, g:i a")
        );
    }
}
