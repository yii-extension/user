<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\App\Runner;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use Throwable;
use Yii\Extension\User\Tests\App\Handler\ThrowableHandler;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\ErrorHandler\Renderer\HtmlRenderer;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

use function microtime;

/**
 * @codeCoverageIgnore
 */
final class ApplicationRunner
{
    private bool $debug = false;

    public function debug(bool $enable = true): void
    {
        $this->debug = $enable;
    }

    public function run(): void
    {
        $startTime = microtime(true);

        $this->debug();

        // Register temporary error handler to catch error while container is building.
        $errorHandler = $this->createErrorHandler();

        $config = $this->createConfig();

        $configContainer = $this->environmentTest($config);

        $container = new Container($configContainer);

        // set aliases tests app
        $this->setAliases($container->get(Aliases::class));

        // Register error handler with real container-configured dependencies.
        $this->registerErrorHandler($container->get(ErrorHandler::class), $errorHandler);

        $container = $container->get(ContainerInterface::class);

        /** @var Application */
        $application = $container->get(Application::class);

        /**
         * @var ServerRequestInterface
         * @psalm-suppress MixedMethodCall
         */
        $serverRequest = $container->get(ServerRequestFactory::class)->createFromGlobals();
        $request = $serverRequest->withAttribute('applicationStartTime', $startTime);

        try {
            $application->start();
            $response = $application->handle($request);
            $this->emit($request, $response);
        } catch (Throwable $throwable) {
            $handler = new ThrowableHandler($throwable);
            /**
             * @var ResponseInterface
             * @psalm-suppress MixedMethodCall
             */
            $response = $container->get(ErrorCatcher::class)->process($request, $handler);
            $this->emit($request, $response);
        } finally {
            $application->afterEmit($response ?? null);
            $application->shutdown();
        }
    }

    private function createConfig(?string $enviroment = null): Config
    {
        return new Config(dirname(__DIR__, 3), '/config/packages', $enviroment);
    }

    private function createErrorHandler(): ErrorHandler
    {
        $errorHandler = new ErrorHandler(new NullLogger(), new HtmlRenderer());

        $this->registerErrorHandler($errorHandler);

        return $errorHandler;
    }

    private function environmentTest(Config $config): array
    {
        return array_merge(
            $config->get('common'),
            $config->get('web'),
            require(dirname(__DIR__, 2) . '/_data/config/psr-http-message.php'),
            require(dirname(__DIR__, 2) . '/_data/config/psr-log.php'),
            require(dirname(__DIR__, 2) . '/_data/config/yiisoft-db.php'),
            require(dirname(__DIR__, 2) . '/_data/config/yiisoft-router.php'),
            require(dirname(__DIR__, 2) . '/_data/config/yiisoft-web.php'),
            require(dirname(__DIR__, 2) . '/_data/config/user.php'),
        );
    }

    private function emit(RequestInterface $request, ResponseInterface $response): void
    {
        (new SapiEmitter())->emit($response, $request->getMethod() === Method::HEAD);
    }

    private function registerErrorHandler(ErrorHandler $registered, ErrorHandler $unregistered = null): void
    {
        if ($unregistered !== null) {
            $unregistered->unregister();
        }

        if ($this->debug) {
            $registered->debug();
        }

        $registered->register();
    }

    private function setAliases(Aliases $aliases): void
    {
        $aliases->set('@root', dirname(__DIR__, 3));
        $aliases->set('@assets', '@root/tests/_data/public/assets');
        $aliases->set('@assetsUrl', '/assets');
        $aliases->set('@npm', '@root/node_modules');
        $aliases->set('@runtime', '@root/tests/_data/runtime');
        $aliases->set('@resources', '@runtime');
        $aliases->set('@translations', '@root/storage/translations');
        $aliases->set('@simple-view-bulma', '@vendor/yii-extension/simple-view-bulma');
        $aliases->set('@vendor', '@root/vendor');
    }
}
