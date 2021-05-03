<?php

declare(strict_types=1);

namespace Yii\Component;

use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\MessageFormatterInterface;

return [
    'categorySourceUser' => static function (Aliases $aliases, MessageFormatterInterface $messageFormatter) {
        $messageReader = new MessageSource($aliases->get('@translations'));

        return new CategorySource('user', $messageReader, $messageFormatter);
    },

    'categorySourceUserMailer' => static function (Aliases $aliases, MessageFormatterInterface $messageFormatter) {
        $messageReader = new MessageSource($aliases->get('@translations'));

        return new CategorySource('user-mailer', $messageReader, $messageFormatter);
    },

    'categorySourceUserView' => static function (Aliases $aliases, MessageFormatterInterface $messageFormatter) {
        $messageReader = new MessageSource($aliases->get('@translations'));

        return new CategorySource('user-view', $messageReader, $messageFormatter);
    },

    IdentityRepositoryInterface::class => RepositoryUser::class,
];
