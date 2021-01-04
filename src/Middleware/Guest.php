<?php

declare(strict_types=1);

namespace Yii\Extension\User\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\User\User;
use Yiisoft\Yii\View\ViewRenderer;

final class Guest implements MiddlewareInterface
{
    private User $user;
    private ViewRenderer $viewRenderer;

    public function __construct(User $user, ViewRenderer $viewRenderer)
    {
        $this->user = $user;
        $this->viewRenderer = $viewRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->user->isGuest() === false) {
            return $this->viewRenderer
                ->withStatus(Status::NOT_FOUND)
                ->withViewPath('@user-view-views')
                ->render('site/404');
        }

        return $handler->handle($request);
    }
}
