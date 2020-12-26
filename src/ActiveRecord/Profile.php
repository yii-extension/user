<?php

declare(strict_types=1);

namespace Yii\Extension\User\ActiveRecord;

use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\ActiveRecord\ActiveQuery;
use Yiisoft\Db\Exception\InvalidArgumentException;

/**
 * ProfileAR Active Record - Module AR User.
 *
 * Database fields:
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $public_email
 * @property string  $location
 * @property string  $website
 * @property string  $bio
 * @property string  $timezone
 */
final class Profile extends ActiveRecord
{
    public function tableName(): string
    {
        return '{{%profile}}';
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function name(?string $value): void
    {
        $this->setAttribute('name', $value);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function publicEmail(?string $value): void
    {
        $this->setAttribute('public_email', $value);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function location(?string $value): void
    {
        $this->setAttribute('location', $value);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function website(?string $value): void
    {
        $this->setAttribute('website', $value);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function bio(?string $value): void
    {
        $this->setAttribute('bio', $value);
    }

    /**
     * @param string|null $value
     *
     * @throws InvalidArgumentException
     */
    public function timezone(?string $value): void
    {
        $this->setAttribute('timezone', $value);
    }
}
