<?php

declare(strict_types=1);

use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\User\User;
use Yiisoft\Factory\Definitions\Reference;

return [
    'yii-extension/view-services' => [
        'defaultParameters' => [
            'setting' => Reference::to(RepositorySetting::class),
            'user' => Reference::to(User::class),
        ]
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@avatars' => '@root/public/images/avatar',
            '@user' => dirname(__DIR__),
        ]
    ],

    'yiisoft/db-sqlite' => [
        'path' => '@storage/yiitest.sq3',
    ],

    'yiisoft/yii-db-migration' => [
        'createNamespace' => 'Yii\\Extension\\User\\Migration',
        'updateNamespace' => [
            'Yii\\Extension\\User\\Migration',
        ]
    ]
];
