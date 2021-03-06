<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Unit;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Yii\Extension\User\Tests\UnitTester;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Db\Migration\Helper\ConsoleHelper;
use Yiisoft\Yii\Db\Migration\Service\MigrationService;

final class MigrationUnitCest
{
    private Config $config;
    private ContainerInterface $container;

    public function _before(UnitTester $I): void
    {
        $this->config = new Config(
            dirname(__DIR__),
            '/config/packages',
        );

        $this->container = new Container(
            $this->config->get('tests/console'),
            $this->config->get('tests/providers')
        );
    }

    public function testMigrationUp(UnitTester $I): void
    {
        $file = dirname(__DIR__) . '/_output/yiitest.sq3';

        $consoleHelper = $this->container->get(ConsoleHelper::class);
        $migration = $this->container->get(MigrationService::class);

        $migration->updateNamespace([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        if (file_exists($file)) {
            FileHelper::unlink($file);
        }

        $params = $this->config->get('tests/params');

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $params['yiisoft/yii-console']['commands']
        );

        $application->setCommandLoader($loader);

        $command = new CommandTester($application->find('migrate/up'));

        $command->setInputs(['yes']);

        $I->assertEquals(ExitCode::OK, $command->execute([]));
    }

    public function testMigrationDown(UnitTester $I): void
    {
        $consoleHelper = $this->container->get(ConsoleHelper::class);
        $migration = $this->container->get(MigrationService::class);

        $migration->updateNamespace([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $params = $this->config->get('tests/params');

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $params['yiisoft/yii-console']['commands']
        );

        $application->setCommandLoader($loader);

        $command = new CommandTester($application->find('migrate/down'));

        $command->setInputs(['yes']);

        $I->assertEquals(ExitCode::OK, $command->execute([]));
    }

    public function testMigration(UnitTester $I): void
    {
        $consoleHelper = $this->container->get(ConsoleHelper::class);
        $migration = $this->container->get(MigrationService::class);

        $migration->updateNamespace([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $params = $this->config->get('tests/params');

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $params['yiisoft/yii-console']['commands']
        );

        $application->setCommandLoader($loader);

        $command = new CommandTester($application->find('migrate/up'));

        $command->setInputs(['yes']);

        $I->assertEquals(ExitCode::OK, $command->execute([]));
    }
}
