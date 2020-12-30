<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\Http\Message\ResponseInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogout;
use Yiisoft\User\User;

final class Logout
{
    public function run(
        RepositoryUser $repositoryUser,
        ServiceLogout $serviceLogout,
        ServiceUrl $serviceUrl,
        User $user
    ): ResponseInterface {
        $serviceLogout->run($repositoryUser, $user);

        return $serviceUrl->run('site/index');
    }
}
