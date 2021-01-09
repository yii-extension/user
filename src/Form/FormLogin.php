<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormLogin extends FormModel
{
    private string $login = '';
    private string $password = '';
    private bool $remember = false;
    private RepositorySetting $repositorySetting;
    private TranslatorInterface $translator;
    private int $lastLogout;

    public function __construct(
        RepositorySetting $repositorySetting,
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->repositorySetting = $repositorySetting;
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('Username'),
            'password' => $this->translator->translate('Password'),
            'remember' => $this->translator->translate('Remember me'),
        ];
    }

    public function formName(): string
    {
        return 'Login';
    }

    public function getLastLogout(): int
    {
        return $this->lastLogout;
    }

    public function getLogin(): string
    {
        if (!$this->repositorySetting->getUserNameCaseSensitive()) {
            $this->login = strtolower($this->login);
        }

        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRemember(): bool
    {
        return $this->remember;
    }

    public function lastLogout(int $value): void
    {
        $this->lastLogout = $value;
    }

    public function rules(): array
    {
        return [
            'login' => [new Required()],
            'password' => [new Required()],
            'remember' => [new Boolean()]
        ];
    }
}
