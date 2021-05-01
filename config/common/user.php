<?php

declare(strict_types=1);

namespace Yii\Component;

use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\Message\Php\MessageSource;

return [
    'categorySourceUser' => static function (Aliases $aliases, MessageFormatterInterface $messageFormatter) {
        $messageReader = new MessageSource($aliases->get('@translations'));

        return new CategorySource('user', $messageReader, $messageFormatter);
    },

    IdentityRepositoryInterface::class => RepositoryUser::class,
];
