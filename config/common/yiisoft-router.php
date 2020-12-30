<?php

declare(strict_types=1);

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\FastRoute\UrlMatcher;

/** @var array $params */

return [
    UrlMatcherInterface::class => UrlMatcher::class,
    UrlGeneratorInterface::class => UrlGenerator::class,
];
