<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Auth;

use Psr\Http\Message\ResponseInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\NotSupportedException;
use Yiisoft\User\CurrentUser;

final class Logout
{
    /**
     * @throws Exception|NotSupportedException
     */
    public function run(
        CurrentUser $currentUser,
        RepositoryUser $repositoryUser,
        ServiceUrl $serviceUrl
    ): ResponseInterface {
        $id = $currentUser->getId();

        if ($id !== null) {
            /** @var User $user */
            $user = $repositoryUser->findUserById($id);
            $user->updateAttributes(['last_logout_at' => time()]);
            $currentUser->logout();
        }

        return $serviceUrl->run('home');
    }
}
