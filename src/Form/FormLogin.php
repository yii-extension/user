<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormLogin extends FormModel
{
    private string $login = '';
    private string $password = '';
    private RepositorySetting $repositorySetting;

    public function __construct(RepositorySetting $repositorySetting, ValidatorFactoryInterface $validatorFactory)
    {
        $this->repositorySetting = $repositorySetting;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'login' => 'Login',
            'password' => 'Password'
        ];
    }

    public function formName(): string
    {
        return 'Login';
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

    public function rules(): array
    {
        return [
            'login' => [new Required()],
            'password' => [new Required()]
        ];
    }
}
