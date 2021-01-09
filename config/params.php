<?php

declare(strict_types=1);

return [
    'yiisoft/aliases' => [
        'aliases' => [
            '@avatars' => '@assets/images/avatar',
            '@user' => dirname(__DIR__),
        ]
    ],

    'yiisoft/yii-db-migration' => [
        'createNamespace' => 'Yii\\Extension\\User\\Migration',
        'updateNamespace' => [
            'Yii\\Extension\\User\\Migration',
        ]
    ],
];
