<?php

declare(strict_types=1);

namespace Yii\Extension\User\Event;

use Yii\Extension\User\Settings\RepositorySetting;
use Yii\Extension\Service\ServiceFlashMessage;

final class AfterConfirm
{
    public function addFlash(
        RepositorySetting $repositorySetting,
        ServiceFlashMessage $serviceFlashMessage
    ): void {
        $serviceFlashMessage->run(
            'success',
            $repositorySetting->getMessageHeader(),
            'Your user has been confirmed.'
        );
    }
}
