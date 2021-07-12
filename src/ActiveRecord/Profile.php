<?php

declare(strict_types=1);

namespace Yii\Extension\User\ActiveRecord;

use Yiisoft\ActiveRecord\ActiveQuery;
use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\Db\Exception\InvalidArgumentException;

/**
 * ProfileAR Active Record - Module AR User.
 *
 * Database fields:
 *
 * @property int $user_id
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

    public function getBio(): string
    {
        return (string) $this->getAttribute('bio');
    }

    public function getLocation(): string
    {
        return (string) $this->getAttribute('location');
    }

    public function getPublicEmail(): string
    {
        return (string) $this->getAttribute('public_email');
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getTimezone(): string
    {
        return (string) $this->getAttribute('timezone');
    }

    public function getWebsite(): string
    {
        return (string) $this->getAttribute('website');
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function name(string $value): void
    {
        $this->setAttribute('name', $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function publicEmail(string $value): void
    {
        $this->setAttribute('public_email', $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function location(string $value): void
    {
        $this->setAttribute('location', $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function website(string $value): void
    {
        $this->setAttribute('website', $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function bio(string $value): void
    {
        $this->setAttribute('bio', $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function timezone(string $value): void
    {
        $this->setAttribute('timezone', $value);
    }
}
