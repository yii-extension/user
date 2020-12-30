<?php

declare(strict_types=1);

namespace Yii\Extension\User\Migration;

use Yiisoft\Yii\Db\Migration\Migration;
use Yiisoft\Yii\Db\Migration\RevertibleMigrationInterface;

/**
 * Handles the creation of table `token`.
 */
final class M200605195559CreateTokenTable extends Migration implements RevertibleMigrationInterface
{
    public function up(): void
    {
        $tableOptions = null;
        if ($this->db->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            'token',
            [
                'user_id' => $this->primaryKey(),
                'code' => $this->string(32),
                'created_at' => $this->integer(),
                'type' => $this->smallInteger(),
            ],
            $tableOptions
        );

        $this->createIndex('token_unique', 'token', ['user_id', 'code', 'type'], true);

        $this->addForeignKey(
            'fk_user_token',
            'token',
            ['user_id'],
            'user',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
    }

    public function down(): void
    {
        $this->dropTable('token');
    }
}
