<?php

declare(strict_types=1);

return [
    'user' => [
        'router' => [
            'prefix' => null,
        ],
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@avatars' => '@assets/images/avatar',
            '@user' => dirname(__DIR__),
            '@user-view-error' => 'views',
        ],
    ],

    'yiisoft/yii-db-migration' => [
        'createNamespace' => 'Yii\\Extension\\User\\Migration',
        'updateNamespace' => [
            'Yii\\Extension\\User\\Migration',
        ],
    ],
];
