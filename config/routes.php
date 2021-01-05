<?php

declare(strict_types=1);

use Yii\Extension\User\Action\Auth\Login;
use Yii\Extension\User\Action\Auth\Logout;
use Yii\Extension\User\Action\Profile\Profile;
use Yii\Extension\User\Action\Recovery\Request;
use Yii\Extension\User\Action\Recovery\Resend;
use Yii\Extension\User\Action\Recovery\Reset;
use Yii\Extension\User\Action\Registration\Confirm;
use Yii\Extension\User\Action\Registration\Register;
use Yiisoft\Auth\Middleware\Authentication;
use Yii\Extension\User\Middleware\Guest;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Route::get('/confirm[/{id}/{code}]', [Confirm::class, 'run'])
        ->name('confirm')
        ->addMiddleware(Guest::class),
    Route::methods(['GET', 'POST'], '/login', [Login::class, 'run'])
        ->name('login')
        ->addMiddleware(Guest::class),
    Route::post('/logout', [Logout::class, 'run'])
        ->name('logout'),
    Route::methods(['GET', 'POST'], '/profile', [Profile::class, 'run'])
        ->name('profile')
        ->addMiddleware(Authentication::class),
    Route::methods(['GET', 'POST'], '/request', [Request::class, 'run'])
        ->name('request')
        ->addMiddleware(Guest::class),
    Route::methods(['GET', 'POST'], '/register', [Register::class, 'run'])
        ->name('register')
        ->addMiddleware(Guest::class),
    Route::methods(['GET', 'POST'], '/resend', [Resend::class, 'run'])
        ->name('resend')
        ->addMiddleware(Guest::class),
    Route::methods(['GET', 'POST'], '/reset[/{id}/{code}]', [Reset::class, 'run'])
        ->name('reset')
        ->addMiddleware(Guest::class),
];
