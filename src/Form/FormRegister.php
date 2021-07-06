<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\Simple\Model\BaseModel;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\MatchRegularExpression;
use Yiisoft\Validator\Rule\Required;

use function strtolower;

final class FormRegister extends BaseModel
{
    private string $email = '';
    private string $username = '';
    private string $password = '';
    private string $ip = '';
    private ModuleSettings $moduleSettings;
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;

    public function __construct(
        ModuleSettings $moduleSettings,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator
    ) {
        $this->repositoryUser = $repositoryUser;
        $this->moduleSettings = $moduleSettings;
        $this->translator = $translator;

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'email' => $this->translator->translate('Email', [], 'user'),
            'username' => $this->translator->translate('Username', [], 'user'),
            'password' => $this->translator->translate('Password', [], 'user'),
        ];
    }

    public function getFormName(): string
    {
        return 'Register';
    }

    public function getEmail(): string
    {
        return strtolower($this->email);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        if (!$this->moduleSettings->getUsernameCaseSensitive()) {
            $this->username = strtolower($this->username);
        }

        return $this->username;
    }

    public function ip(string $value): void
    {
        $this->ip = $value;
    }

    public function password(string $value): void
    {
        $this->password = $value;
    }

    public function getRules(): array
    {
        return [
            'email' => $this->emailRules(),
            'username' => $this->usernameRules(),
            'password' => $this->passwordRules(),
        ];
    }

    private function emailRules(): array
    {
        return [
            Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),
            Email::rule()->message($this->translator->translate('This value is not a valid email address', [], 'user')),

            function (): Result {
                $result = new Result();

                if ($this->repositoryUser->findUserByUsernameOrEmail($this->email)) {
                    $result->addError($this->translator->translate('Email already registered', [], 'user'));
                }

                return $result;
            },
        ];
    }

    private function usernameRules(): array
    {
        $matchRegularExpression = MatchRegularExpression::rule($this->moduleSettings->getUsernameRegExp());

        return [
            Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),
            HasLength::rule()->min(3)->max(255)->tooShortMessage(
                $this->translator->translate('Username should contain at least 3 characters', [], 'user'),
            ),
            $matchRegularExpression->message($this->translator->translate('This value is invalid', [], 'user')),

            function (): Result {
                $result = new Result();

                if ($this->repositoryUser->findUserByUsernameOrEmail($this->username)) {
                    $result->addError($this->translator->translate('Username already registered', [], 'user'));
                }

                return $result;
            },
        ];
    }

    private function passwordRules(): array
    {
        $result = [];

        if ($this->moduleSettings->isGeneratingPassword() === false) {
            $result = [
                Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),
                HasLength::rule()->min(6)->max(72)->tooShortMessage(
                    $this->translator->translate(
                        'Password should contain at least 6 characters',
                        [],
                        'user'
                    )
                ),
            ];
        }

        return $result;
    }
}
