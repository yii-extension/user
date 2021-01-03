<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormRequest extends FormModel
{
    private string $email = '';
    private Translator $translator;

    public function __construct(
        Translator $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'email' => $this->translator->translate('Email'),
        ];
    }

    public function formName(): string
    {
        return 'Request';
    }

    public function getEmail(): string
    {
        return strtolower($this->email);
    }

    public function rules(): array
    {
        return [
            'email' => [
                new Required(),
                new Email()
            ]
        ];
    }
}
