<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorFactoryInterface;

final class FormReset extends FormModel
{
    private string $password = '';
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'password' => $this->translator->translate('Password'),
        ];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function formName(): string
    {
        return 'Reset';
    }

    public function rules(): array
    {
        return [
            'password' => [
                (new Required())->message($this->translator->translate('Value cannot be blank')),
                (new HasLength())->min(6)->max(72)->tooShortMessage('Password should contain at least 6 characters')
            ],
        ];
    }
}
