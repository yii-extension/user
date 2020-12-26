<?php

declare(strict_types=1);

use Yii\Extension\User\Action\Auth\Login;
use Yii\Extension\User\Action\Auth\Logout;
use Yii\Extension\User\Action\Registration\Confirm;
use Yii\Extension\User\Action\Registration\Register;
use Yii\Extension\User\Action\Registration\Resend;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Router\Route;

return [
    Route::get('/confirm[/{id}/{code}]', [Confirm::class, 'run'])->name('confirm'),
    Route::methods(['GET', 'POST'], '/login', [Login::class, 'run'])->name('login'),
    Route::post('/logout', [Logout::class, 'run'])->name('logout'),
    Route::methods(['GET', 'POST'], '/register', [Register::class, 'run'])->name('register'),
    Route::methods(['GET', 'POST'], '/resend', [Resend::class, 'run'])->name('resend'),
];
