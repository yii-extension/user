<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\User\User as IdentityUser;

final class ServiceLogout
{
    public function run(RepositoryUser $repositoryUser, IdentityUser $identityUser): bool
    {
        /** @var User $user */
        $user = $repositoryUser->findUserById($identityUser->getId());

        $user->updateAttributes(['last_logout_at' => time()]);

        return $identityUser->logout();
    }
}
