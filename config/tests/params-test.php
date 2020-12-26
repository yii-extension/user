<?php

declare(strict_types=1);

use Yiisoft\Arrays\Modifier\ReverseBlockMerge;

return [
    'yiisoft/db-sqlite' => [
        'path' => '@user/tests/_data/yiitest.sq3',
    ],
    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
