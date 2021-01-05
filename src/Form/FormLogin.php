<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\Translator;
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
    private Translator $translator;
    private int $lastLogin;

    public function __construct(
        RepositorySetting $repositorySetting,
        Translator $translator,
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

    public function getLastLogin(): int
    {
        return $this->lastLogin;
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

    public function lastLogin(int $value): void
    {
        $this->lastLogin = $value;
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
