<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Yii\Extension\User\Tests\App\Command\Hello;
use Yii\Extension\User\Tests\App\ViewInjection\ContentViewInjection;
use Yii\Extension\User\Tests\App\ViewInjection\LayoutViewInjection;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Profiler\Target\FileTarget;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'app' => [
        'charset' => 'UTF-8',
        'locale' => 'en',
        'name' => 'My Project',
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@assets' => '@root/public/assets',
            '@assetsUrl' => '/assets',
            '@npm' => dirname(__DIR__, 3) . '/vendor/npm-asset',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '../../_output/runtime',
            '@views' => InstalledVersions::isInstalled('yii-extension/user-view-bulma')
                ? '@resources/views/bulma' : '@resources/views/bootstrap5',
            '@message' => '@root/resources/message',
            'user-view-error' => '@views',
        ],
    ],

    'yiisoft/form' => [
        'bootstrap5' => [
            'enabled' => InstalledVersions::isInstalled('yii-extension/user-view-bootstrap5'),
        ],
        'bulma' => [
            'enabled' => InstalledVersions::isInstalled('yii-extension/user-view-bulma'),
        ]
    ],

    'yiisoft/profiler' => [
        'targets' => [
            FileTarget::class => [
                'enabled' => true,
            ],
        ],
    ],

    'yiisoft/translator' => [
        'locale' => 'en',
    ],

    'yiisoft/view' => [
        'basePath' => '@views',
    ],

    'yiisoft/yii-console' => [
        'commands' => [
            'hello' => Hello::class,
        ],
    ],

    'yiisoft/yii-debug' => [
        'enabled' => true,
    ],

    'yiisoft/yii-view' => [
        'viewBasePath' => '@views',
        'layout' => InstalledVersions::isInstalled('yii-extension/user-view-bulma')
            ? '@resources/layout/bulma/main' : '@resources/layout/bootstrap5/main',
        'injections' => [
            Reference::to(ContentViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
        ],
    ],

    'yiisoft/router' => [
        'enableCache' => false,
    ],

    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
