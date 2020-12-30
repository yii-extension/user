<?php

declare(strict_types=1);

namespace Yii\Extension\User\Migration;

use Yiisoft\Yii\Db\Migration\Migration;
use Yiisoft\Yii\Db\Migration\RevertibleMigrationInterface;

/**
 * Handles the creation of table `social_account`.
 */
final class M200605195402CreateSocialAccountTable extends Migration implements RevertibleMigrationInterface
{
    public function up(): void
    {
        $tableOptions = null;
        if ($this->db->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            'social_account',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'provider' => $this->string(255),
                'client_id' => $this->string(255),
                'data' => $this->text(),
                'code' => $this->string(32),
                'created_at' => $this->integer(),
                'email' => $this->string(255),
                'username' => $this->string(255),
            ],
            $tableOptions
        );

        $this->createIndex('account_unique', 'social_account', ['provider', 'client_id'], true);
        $this->createIndex('account_unique_code', 'social_account', ['code'], true);

        $this->addForeignKey(
            'fk_user_account',
            'social_account',
            ['user_id'],
            'user',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
    }

    public function down(): void
    {
        $this->dropTable('social_account');
    }
}
