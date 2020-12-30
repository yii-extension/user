<?php

declare(strict_types=1);

namespace Yii\Extension\User\Migration;

use Yiisoft\Yii\Db\Migration\Migration;
use Yiisoft\Yii\Db\Migration\RevertibleMigrationInterface;

/**
 * Handles the creation of table `user`.
 */
final class M200601191317CreateUserTable extends Migration implements RevertibleMigrationInterface
{
    public function up(): void
    {
        $tableOptions = null;

        if ($this->db->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            'user',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(255),
                'email' => $this->string(255),
                'password_hash' => $this->string(100),
                'auth_key' => $this->string(32),
                'confirmed_at' => $this->integer(),
                'unconfirmed_email' => $this->string(255),
                'blocked_at' => $this->integer(),
                'registration_ip' => $this->string(45),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'flags' => $this->integer(),
                'ip_last_login' => $this->string(45),
                'last_login_at' => $this->integer(),
                'last_logout_at' => $this->integer(),
            ],
            $tableOptions
        );

        $this->createIndex('user_unique_email', 'user', ['email'], true);
        $this->createIndex('user_unique_username', 'user', ['username'], true);
    }

    public function down(): void
    {
        $this->dropTable('user');
    }
}
