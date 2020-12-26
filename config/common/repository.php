<?php

declare(strict_types=1);

namespace Yii\Component;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Psr\Log\LoggerInterface;
use Yii\Extension\User\ActiveRecord\Profile;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Factory\Definitions\Reference;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => [
        '__class' => RepositoryUser::class,
        '__construct()' => [
            Reference::to(Aliases::class),
            Reference::to(InitialAvatar::class),
            Reference::to(ConnectionInterface::class),
            Reference::to(LoggerInterface::class),
            Reference::to(Profile::class),
            Reference::to(Token::class),
            Reference::to(User::class),
        ]
    ]
];
