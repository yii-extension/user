<?php

declare(strict_types=1);

namespace Yii\Component;

use Yii\Extension\User\Settings\SettingsDto;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
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

    /** Config yii-extension-user */
    ModuleSettings::class => [
        'class' => SettingsDto::class,
        '__construct()' => [
            /* bool Whether user has to confirm his account. */
            false,
            /* bool Whether user can remove his account */
            false,
            /* bool Whether to remove password field from registration form. */
            false,
            /* bool Whether to enable password recovery. */
            true,
            /* bool Whether to enable registration. */
            true,
            /* int The time before a confirmation token becomes invalid. */
            86400,
            /* int The time before a recovery token becomes invalid. */
            21600,

            true,
            /* string Regex username */
            '/^[-a-zA-Z0-9_\.@]+$/',
            /**
             * STRATEGY_INSECURE is changed right after user enter's new email address.
             * STRATEGY_DEFAULT is changed after user clicks confirmation link sent to his new email address.
             * STRATEGY_SECURE is changed after user clicks both confirmation links sent to his old and new email
             * addresses.
             */
            SettingsDto::STRATEGY_DEFAULT,
        ],
    ],
];
