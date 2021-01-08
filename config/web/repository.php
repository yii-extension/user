<?php

declare(strict_types=1);

namespace Yii\Component;

use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Auth\IdentityRepositoryInterface;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => RepositoryUser::class,
];
