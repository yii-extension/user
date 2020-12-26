<?php

declare(strict_types=1);

use Yiisoft\Cache\Cache;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Factory\Definitions\Reference;

/** @var array $params */

return [
    CacheInterface::class => [
        '__class' => Cache::class,
        '__construct()' => [
            Reference::to(FileCache::class)
        ]
    ],
];
