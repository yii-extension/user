<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\ErrorHandler\Renderer\HtmlRenderer;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

$c3 = dirname(__DIR__, 3) . '/c3.php';

if (is_file($c3)) {
    require $c3;
}

// PHP built-in server routing.
if (PHP_SAPI === 'cli-server') {
    // Serve static files as is.
    if (is_file(__DIR__ . $_SERVER['REQUEST_URI'])) {
        return false;
    }

    // Explicitly set for URLs with dot.
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$startTime = microtime(true);

/**
 * Register temporary error handler to catch error while container is building.
 */
$errorHandler = new ErrorHandler(new NullLogger(), new HtmlRenderer());
// Development mode:
$errorHandler->debug();
$errorHandler->register();

$config = new Config(
    dirname(__DIR__),
    '/config/packages',
);

$container = new Container(
    $config->get('tests/web'),
    $config->get('tests/providers-web')
);

/**
 * Configure error handler with real container-configured dependencies.
 */
$errorHandler->unregister();
$errorHandler = $container->get(ErrorHandler::class);
// Development mode:
$errorHandler->debug();
$errorHandler->register();

$container = $container->get(ContainerInterface::class);
$application = $container->get(Application::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();
$request = $request->withAttribute('applicationStartTime', $startTime);

try {
    $application->start();
    $response = $application->handle($request);
    $emitter = new SapiEmitter();
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->afterEmit($response ?? null);
    $application->shutdown();
}
