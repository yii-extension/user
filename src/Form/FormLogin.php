<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\User as Identity;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormLogin extends FormModel
{
    private string $login = '';
    private string $password = '';
    private bool $remember = false;
    private Identity $identity;
    private string $ip = '';
    private int $lastLogout;
    private RepositoryUser $repositoryUser;
    private RepositorySetting $repositorySetting;
    private TranslatorInterface $translator;
    private ?User $user = null;

    public function __construct(
        Identity $identity,
        RepositoryUser $repositoryUser,
        RepositorySetting $repositorySetting,
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->identity = $identity;
        $this->repositoryUser = $repositoryUser;
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

    public function ip(string $value): void
    {
        $this->ip = $value;
    }

    public function getLastLogout(): int
    {
        return $this->lastLogout;
    }

    public function rules(): array
    {
        return [
            'login' => [new Required()],
            'password' => $this->passwordRules(),
            'remember' => [new Boolean()]
        ];
    }

    public function validate(): bool
    {
        if (!$this->repositorySetting->getUserNameCaseSensitive()) {
            $this->login = strtolower($this->login);
        }

        $this->user = $this->repositoryUser->findUserByUsernameOrEmail($this->login);

        return parent::validate();
    }

    private function passwordRules(): array
    {
        $passwordHasher = new PasswordHasher();

        return [
            new Required(),
            function () use ($passwordHasher): Result {
                $result = new Result();

                if ($this->user === null) {
                    $result->addError($this->translator->translate('Unregistered user/Invalid password'));
                }

                if ($this->user !== null && $this->user->isBlocked()) {
                    $result->addError(
                        $this->translator->translate('Your user has been blocked, contact an administrator')
                    );
                }

                if ($this->user !== null && !$this->user->isConfirmed()) {
                    $result->addError(
                        $this->translator->translate('Please check your email to activate your account')
                    );
                }

                if ($this->user !== null && !$passwordHasher->validate($this->password, $this->user->getPasswordHash())) {
                    $result->addError($this->translator->translate('Unregistered user/Invalid password'));
                }

                if ($result->isValid()) {
                    $this->lastLogout = $this->user->getLastLogout();
                    $this->user->updateAttributes(['ip_last_login' => $this->ip, 'last_login_at' => time()]);
                    $this->identity->login($this->user);
                }

                return $result;
            },
        ];
    }
}
