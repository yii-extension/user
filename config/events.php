<?php

declare(strict_types=1);

use Yii\Extension\Service\Event\MessageSent;
use Yii\Extension\User\Event\AfterLogin;
use Yii\Extension\User\Event\AfterResend;
use Yii\Extension\User\Event\MessageSentHandler;

return [
    AfterLogin::class => [[AfterLogin::class, 'addFlash']],
    MessageSent::class => [[MessageSentHandler::class, 'addFlash']],
];
