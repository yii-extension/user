<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Unit;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Yii\Extension\User\Tests\UnitTester;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Db\Migration\Helper\ConsoleHelper;
use Yiisoft\Yii\Db\Migration\Service\MigrationService;

final class MigrationUnitCest
{
    private ContainerInterface $container;
    private array $params;

    public function _before(UnitTester $I): void
    {
        $config = new Config(
            dirname(__DIR__, 2),
            '/config/packages', // Configs path.
        );

        $this->params = $config->get('params');

        $this->container = new Container(
            array_merge(
                require(dirname(__DIR__) . '/_data/config/yiisoft-db.php'),
                $config->get('common'),
                $config->get('console'),
            ),
        );

        // set aliases tests app
        $aliases = $this->container->get(Aliases::class);
        $aliases->set('@root', dirname(__DIR__, 2));
        $aliases->set('@assets', '@root/tests/_data/public/assets');
        $aliases->set('@assetsUrl', '/assets');
        $aliases->set('@npm', '@root/vendor/npm-asset');
        $aliases->set('@runtime', '@root/tests/_data/runtime');
        $aliases->set('@translations', '@root/storage/translations');
        $aliases->set('@simple-view-bulma', '@root');
    }

    public function testMigrationUp(UnitTester $I): void
    {
        $file = dirname(__DIR__) . '/_output/yiitest.sq3';

        $consoleHelper = $this->container->get(ConsoleHelper::class);
        $migration = $this->container->get(MigrationService::class);

        $migration->updateNamespaces([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        if (file_exists($file)) {
            FileHelper::unlink($file);
        }

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $this->params['yiisoft/yii-console']['commands']
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

        $migration->updateNamespaces([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $this->params['yiisoft/yii-console']['commands']
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

        $migration->updateNamespaces([
            'Yii\Extension\User\Migration',
            'Yii\Extension\User\Settings\Migration',
        ]);

        $consoleHelper->output()->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $application = $this->container->get(Application::class);

        $loader = new ContainerCommandLoader(
            $this->container,
            $this->params['yiisoft/yii-console']['commands']
        );

        $application->setCommandLoader($loader);

        $command = new CommandTester($application->find('migrate/up'));

        $command->setInputs(['yes']);

        $I->assertEquals(ExitCode::OK, $command->execute([]));
    }
}
