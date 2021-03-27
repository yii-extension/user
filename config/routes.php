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
    Group::create($params['user']['router']['prefix'])->routes(
        Route::get('/email/attempt[/{id}/{code}]')
            ->action([AttemptEmailChange::class, 'run'])
            ->name('email/attempt'),
        Route::methods(['GET', 'POST'], '/email/change')
            ->middleware(Authentication::class)
            ->action([EmailChange::class, 'run'])
            ->name('email/change'),
        Route::get('/confirm[/{id}/{code}]')
            ->middleware(Guest::class)
            ->action([Confirm::class, 'run'])
            ->name('confirm'),
        Route::methods(['GET', 'POST'], '/login')
            ->middleware(Guest::class)
            ->action([Login::class, 'run'])
            ->name('login'),
        Route::post('/logout')
            ->action([Logout::class, 'run'])
            ->name('logout'),
        Route::methods(['GET', 'POST'], '/profile')
            ->middleware(Authentication::class)
            ->action([Profile::class, 'run'])
            ->name('profile'),
        Route::methods(['GET', 'POST'], '/request')
            ->middleware(Guest::class)
            ->action([Request::class, 'run'])
            ->name('request'),
        Route::methods(['GET', 'POST'], '/register')
            ->middleware(Guest::class)
            ->action([Register::class, 'run'])
            ->name('register'),
        Route::methods(['GET', 'POST'], '/resend')
            ->middleware(Guest::class)
            ->action([Resend::class, 'run'])
            ->name('resend'),
        Route::methods(['GET', 'POST'], '/reset[/{id}/{code}]')
            ->middleware(Guest::class)
            ->action([Reset::class, 'run'])
            ->name('reset'),
    ),
];
