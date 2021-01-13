<?php

declare(strict_types=1);

use Yii\Extension\User\Tests\App\Controller\SiteController;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Router\Route;

return [
    Route::get('/', [SiteController::class, 'index'])->name('home'),
    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
