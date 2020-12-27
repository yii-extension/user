<?php

declare(strict_types=1);

use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\User\User;
use Yiisoft\Factory\Definitions\Reference;

return [
    'app' => [
        'charset' => 'UTF-8',
        'emailFrom' => 'tester@example.com',
        'language' => 'en',
        'logo' => '/images/yii-logo.jpg',

        /** config widget nav */
        'nav' => [
            'guest' => [],
            'logged' => [],
        ],

        /** config widget navBar */
        'navBar' => [
            'config' => [
                'brandLabel()' => ['My Project'],
                'brandImage()' => ['/images/yii-logo.jpg'],
                'itemsOptions()' => [['class' => 'navbar-end']],
                'options()' => [['class' => 'is-black', 'data-sticky' => '', 'data-sticky-shadow' => '']]                    ],
        ],

        'name' => 'My Project',
    ],

    'yii-extension/view-services' => [
        'defaultParameters' => [
            'setting' => Reference::to(RepositorySetting::class),
            'user' => Reference::to(User::class),
        ]
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__, 3),
            '@storage' => dirname(__DIR__) . '/storage',
            '@assets' =>  dirname(__DIR__) . '/public/assets',
            '@assetsUrl' => '/assets',
            '@avatars' => dirname(__DIR__) . '/public/images/avatar',
            '@layout' => '@storage/views/layout',
            '@npm' => dirname(__DIR__, 3) . '/vendor/npm-asset',
            '@runtime' => dirname(__DIR__) . '/runtime',
            '@user' => dirname(__DIR__, 3),
            '@views' => '@storage/views',
        ]
    ],

    'yiisoft/view' => [
        'basePath' => '@views',
    ],

    'yiisoft/yii-db-migration' => [
        'createNamespace' => 'Yii\\Extension\\User\\Migration',
        'updateNamespace' => [
            'Yii\\Extension\\User\\Migration',
        ]
    ]
];
