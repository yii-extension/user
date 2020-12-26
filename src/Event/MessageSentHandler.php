<?php

declare(strict_types=1);

namespace Yii\Extension\User\Event;

use Yii\Extension\Service\ServiceFlashMessage;
use Yii\Extension\Service\Event\MessageSent;

final class MessageSentHandler
{
    public function addFlash(
        MessageSent $messageSent,
        ServiceFlashMessage $serviceFlashMessage
    ): void {
        if ($messageSent->getAddFlash()) {
            $serviceFlashMessage->run(
                $messageSent->getType(),
                $messageSent->getHeader(),
                $messageSent->getBody()
            );
        }
    }
}
