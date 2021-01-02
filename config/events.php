<?php

declare(strict_types=1);

use Yii\Extension\Service\Event\MessageSent;
use Yii\Extension\User\Event\MessageSentHandler;

return [
    MessageSent::class => [[MessageSentHandler::class, 'addFlash']],
];
