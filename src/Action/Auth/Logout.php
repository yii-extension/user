<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\Http\Message\ResponseInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceLogout;
use Yiisoft\User\User as IdentityUser;

final class Logout
{
    public function run(
        IdentityUser $identityUser,
        RepositoryUser $repositoryUser,
        ServiceLogout $serviceLogout,
        ServiceUrl $serviceUrl
    ): ResponseInterface {
        $serviceLogout->run($repositoryUser, $identityUser);

        return $serviceUrl->run('index');
    }
}
