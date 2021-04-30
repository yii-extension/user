<?php

declare(strict_types=1);

use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Factory\Definition\Reference;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\NotFoundHandler;

return [
    Application::class => [
        'class' => Application::class,
        '__construct()' => [
            'dispatcher' => static fn (MiddlewareDispatcher $middlewareDispatcher) =>
                $middlewareDispatcher->withMiddlewares(
                    [
                        Router::class,
                        SessionMiddleware::class,
                        ErrorCatcher::class,
                    ]
                ),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],
];
