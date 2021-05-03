<?php

declare(strict_types=1);

use Yiisoft\Factory\Definition\Reference;

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
        ],
    ],

    'yiisoft/yii-db-migration' => [
        'updateNamespaces' => [
            'Yii\\Extension\\User\\Migration',
        ],
    ],

    'yiisoft/translator' => [
        'categorySources' => [
            Reference::to('categorySourceUser'),
            Reference::to('categorySourceUserMailer'),
            Reference::to('categorySourceUserView'),
        ],
    ],
];
