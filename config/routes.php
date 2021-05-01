<?php

declare(strict_types=1);

use Yii\Extension\User\Action\Auth\Login;
use Yii\Extension\User\Action\Auth\Logout;
use Yii\Extension\User\Action\Email\AttemptEmailChange;
use Yii\Extension\User\Action\Email\EmailChange;
use Yii\Extension\User\Action\Profile\Profile;
use Yii\Extension\User\Action\Recovery\Request;
use Yii\Extension\User\Action\Recovery\Resend;
use Yii\Extension\User\Action\Recovery\Reset;
use Yii\Extension\User\Action\Registration\Confirm;
use Yii\Extension\User\Action\Registration\Register;
use Yii\Extension\User\Middleware\Guest;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Config\Config;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

/** @var Config $config */

$params = $config->get('params');

return [
    Group::create($params['user']['router']['prefix'])
        ->routes(
            Route::get('/email/attempt[/{id}/{code}]')
                ->action([AttemptEmailChange::class, 'run'])
                ->name('email/attempt'),
            Route::methods(['GET', 'POST'], '/email/change')
                ->name('email/change')
                ->middleware(Authentication::class)
                ->action([EmailChange::class, 'run']),
            Route::get('/confirm[/{id}/{code}]')
                ->name('confirm')
                ->middleware(Guest::class)
                ->action([Confirm::class, 'run']),
            Route::methods(['GET', 'POST'], '/login')
                ->name('login')
                ->middleware(Guest::class)
                ->action([Login::class, 'run']),
            Route::post('/logout')
                ->action([Logout::class, 'run'])
                ->name('logout'),
            Route::methods(['GET', 'POST'], '/profile')
                ->name('profile')
                ->middleware(Authentication::class)
                ->action([Profile::class, 'run']),
            Route::methods(['GET', 'POST'], '/request')
                ->name('request')
                ->middleware(Guest::class)
                ->action([Request::class, 'run']),
            Route::methods(['GET', 'POST'], '/register')
                ->name('register')
                ->middleware(Guest::class)
                ->action([Register::class, 'run']),
            Route::methods(['GET', 'POST'], '/resend')
                ->name('resend')
                ->middleware(Guest::class)
                ->action([Resend::class, 'run']),
            Route::methods(['GET', 'POST'], '/reset[/{id}/{code}]')
                ->name('reset')
                ->middleware(Guest::class)
                ->action([Reset::class, 'run']),
        ),
];
