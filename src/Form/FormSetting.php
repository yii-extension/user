<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorFactoryInterface;

final class FormSetting extends FormModel
{
    private bool $confirmation = false;
    private bool $delete = false;
    private string $emailFrom = '';
    private bool $generatingPassword = false;
    private string $headerMessage = '';
    private bool $passwordRecovery = true;
    private bool $register = true;
    private string $subjectConfirm = '';
    private string $subjectPassword = '';
    private string $subjectReconfirmation = '';
    private string $subjectRecovery = '';
    private string $subjectWelcome = '';
    private int $tokenConfirmWithin = 0;
    private int $tokenRecoverWithin = 0;
    private bool $usernameCaseSensitive = true;
    private string $usernameRegExp = '';
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function formName(): string
    {
        return 'Settings';
    }

    public function attributeLabels(): array
    {
        return [
            'confirmation' => $this->translator->translate('Confirmation'),
            'delete' => $this->translator->translate('Account Delete'),
            'emailFrom' => $this->translator->translate('Email From'),
            'generatingPassword' => $this->translator->translate('Generating password'),
            'headerMessage' => $this->translator->translate('Header Message'),
            'passwordRecovery' => $this->translator->translate('Password Recovery'),
            'register' => $this->translator->translate('Register'),
            'subjectConfirm' => $this->translator->translate('Subject Confirm'),
            'subjectPassword' => $this->translator->translate('Subject Password'),
            'subjectReconfirmation' => $this->translator->translate('Subject Reconfirmation'),
            'subjectRecovery' => $this->translator->translate('Subject Recovery'),
            'subjectWelcome' => $this->translator->translate('Subject Welcome'),
            'tokenConfirmWithin' => $this->translator->translate('Token Confirmation'),
            'tokenRecoverWithin' => $this->translator->translate('Token Recover'),
            'usernameCaseSensitive' => $this->translator->translate('Username Case Sensitive'),
            'usernameRegExp' => $this->translator->translate('Username Regex Expression'),
        ];
    }
}
