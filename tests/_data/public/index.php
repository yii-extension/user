<?php

declare(strict_types=1);

use Yii\Extension\User\Tests\App\Runner\ApplicationRunner;

$c3 = dirname(__DIR__, 3) . '/c3.php';

if (is_file($c3)) {
    require_once $c3;
}

// PHP built-in server routing.
if (PHP_SAPI === 'cli-server') {
    // Serve static files as is.
    if (is_file(__DIR__ . $_SERVER["REQUEST_URI"])) {
        return false;
    }

    // Explicitly set for URLs with dot.
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$runner = new ApplicationRunner();

// Development mode:
$runner->debug();

// Run application:
$runner->run();
