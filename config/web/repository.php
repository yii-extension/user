<?php

declare(strict_types=1);

namespace Yii\Component;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Psr\Log\LoggerInterface;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\ActiveRecord\ActiveRecordFactory;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Factory\Definitions\Reference;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => [
        '__class' => RepositoryUser::class,
        '__construct()' => [
            Reference::to(ActiveRecordFactory::class),
            Reference::to(Aliases::class),
            Reference::to(InitialAvatar::class),
            Reference::to(LoggerInterface::class),
        ]
    ]
];
