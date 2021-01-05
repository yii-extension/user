<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\ValidatorFactoryInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Url;

final class FormProfile extends FormModel
{
    private ?string $name = '';
    private ?string $publicEmail = '';
    private ?string $location = '';
    private ?string $website = '';
    private ?string $bio = '';
    private ?string $timezone = '';

    public function __construct(
        Translator $translator,
        ValidatorFactoryInterface $validator
    ) {
        parent::__construct($validator);
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'publicEmail' => 'Public email',
            'location' => 'Location',
            'website' => 'Website',
            'bio' => 'Bio',
            'timezone' => 'TimeZone'
        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function name(string $value): void
    {
        $this->name = $value;
    }

    public function getPublicEmail(): ?string
    {
        return $this->publicEmail;
    }

    public function publicEmail(string $value): void
    {
        $this->publicEmail = $value;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function location(string $value): void
    {
        $this->location = $value;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function website(string $value): void
    {
        $this->website = $value;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function bio(string $value): void
    {
        $this->bio = $value;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function timezone(string $value): void
    {
        $this->timezone = $value;
    }

    public function rules(): array
    {
        return [
            'publicEmail' => [(new Email())->skipOnEmpty(true)],
            'website' => [(new Url())->skipOnEmpty(true)],
        ];
    }
}
