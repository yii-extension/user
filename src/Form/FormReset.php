<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\Simple\Model\BaseModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class FormReset extends BaseModel
{
    private string $password = '';
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'password' => $this->translator->translate('Password', [], 'user'),
        ];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFormName(): string
    {
        return 'Reset';
    }

    public function getRules(): array
    {
        return [
            'password' => [
                Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),
                HasLength::rule()->min(6)->max(72)->tooShortMessage(
                    $this->translator->translate('Password should contain at least 6 characters', [], 'user'),
                ),
            ],
        ];
    }
}
