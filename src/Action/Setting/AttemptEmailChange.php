<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Setting;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yii\Extension\Service\ServiceUrl;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Service\ServiceAttemptEmailChange;
use Yiisoft\User\User as Identity;

final class AttemptEmailChange
{
    public function run(
        Identity $identity,
        RepositoryUser $repositoryUser,
        RequestHandlerInterface $requestHandler,
        ServerRequestInterface $serverRequest,
        ServiceAttemptEmailChange $serviceAttemptEmailChange,
        ServiceUrl $serviceUrl
    ): ResponseInterface {
        /** @var string|null $id */
        $id = $serverRequest->getAttribute('id');

        /** @var string|null $code */
        $code = $serverRequest->getAttribute('code');

        if ($id === null  || $code === null || ($user = $repositoryUser->findUserById($id)) === null) {
            return $requestHandler->handle($serverRequest);
        }

        if ($serviceAttemptEmailChange->run($id, $code, $user) === false) {
            return $requestHandler->handle($serverRequest);
        }

        $identity->isGuest() ? $url = 'home' : $url = 'account';

        return $serviceUrl->run($url);
    }
}
