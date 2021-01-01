<?php

declare(strict_types=1);

namespace Yii\Extension\User\Migration;

use Yiisoft\Yii\Db\Migration\Migration;
use Yiisoft\Yii\Db\Migration\RevertibleMigrationInterface;

/**
 * Handles the creation of table `profile`.
 */
final class M200602215007CreateProfileTable extends Migration implements RevertibleMigrationInterface
{
    public function up(): void
    {
        $tableOptions = null;
        if ($this->db->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%profile}}',
            [
                'user_id' => $this->primaryKey(),
                'name' => $this->string(255),
                'public_email' => $this->string(255),
                'location' => $this->string(255),
                'website' => $this->string(255),
                'bio' => $this->text(),
                'timezone' => $this->string(40)
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk_user_profile',
            '{{%profile}}',
            ['user_id'],
            '{{%user}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
    }

    public function down(): void
    {
        $this->dropTable('{{%profile}}');
    }
}
