<?php

declare(strict_types=1);

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Sqlite\Connection;

/** @var array $params */

return [
    ConnectionInterface::class => [
        'class' => Connection::class,
        '__construct()' => [
            'dsn' => 'sqlite:' . dirname(__DIR__, 2) . '/_output/yiitest.sq3',
        ],
    ],
];
