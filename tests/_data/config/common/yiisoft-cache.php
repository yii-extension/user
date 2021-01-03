<?php

declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\File\FileCache;

return [
    CacheInterface::class => [
        '__class' => Cache::class,
        '__construct()' => [
            Reference::to(FileCache::class)
        ]
    ],
];
