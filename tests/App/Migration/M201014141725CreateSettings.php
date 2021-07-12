<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\App\Migration;

use Yiisoft\Yii\Db\Migration\MigrationBuilder;
use Yiisoft\Yii\Db\Migration\RevertibleMigrationInterface;

/**
 * Class M201014141725CreateSettings
 */
final class M201014141725CreateSettings implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $tableOptions = null;

        if ($b->getDb()->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 ENGINE=InnoDB';
        }

        $b->createTable(
            '{{%settings}}',
            [
                'id' => $b->primaryKey(),
                'confirmation' => $b->boolean(),
                'delete' => $b->boolean(),
                'generatingPassword' => $b->boolean(),
                'passwordRecovery' => $b->boolean(),
                'register' => $b->boolean(),
                'tokenConfirmWithin' => $b->integer(),
                'tokenRecoverWithin' => $b->integer(),
                'userNameCaseSensitive' => $b->boolean(),
                'userNameRegExp' => $b->string(25),
                'emailChangeStrategy' => $b->integer(),
            ],
            $tableOptions
        );

        $b->createIndex('id', '{{%settings}}', ['id'], true);

        $b->batchInsert(
            '{{%settings}}',
            [
                'confirmation',
                'delete',
                'generatingPassword',
                'passwordRecovery',
                'register',
                'tokenConfirmWithin',
                'tokenRecoverWithin',
                'userNameCaseSensitive',
                'userNameRegExp',
                'emailChangeStrategy',
            ],
            [
                [
                    false,
                    false,
                    false,
                    true,
                    true,
                    86400,
                    86400,
                    true,
                    '/^[-a-zA-Z0-9_\.@]+$/',
                    1,
                ],
            ]
        );
    }

    public function down(MigrationBuilder $b): void
    {
        $b->dropTable('{{%settings}}');
    }
}
