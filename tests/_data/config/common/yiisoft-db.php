<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Sqlite\Connection;

/** @var array $params */

return [
    ConnectionInterface::class => [
        '__class' => Connection::class,
        '__construct()' => [
            'dsn' => 'sqlite:' . dirname(__DIR__, 3) . '/_output/yiitest.sq3',
        ]
    ]
];
