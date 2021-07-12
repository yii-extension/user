<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\Simple\Model\BaseModel;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

use function strtolower;

final class FormLogin extends BaseModel
{
    private string $login = '';
    private string $password = '';
    private string $ip = '';
    private int $lastLogout = 0;
    private CurrentUser $currentUser;
    private ModuleSettings $moduleSettings;
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;

    public function __construct(
        CurrentUser $currentUser,
        ModuleSettings $moduleSettings,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator
    ) {
        $this->currentUser = $currentUser;
        $this->moduleSettings = $moduleSettings;
        $this->repositoryUser = $repositoryUser;
        $this->translator = $translator;

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('Username', [], 'user'),
            'password' => $this->translator->translate('Password', [], 'user'),
        ];
    }

    public function getFormName(): string
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

    public function getRules(): array
    {
        return [
            'login' => [Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user'))],
            'password' => $this->passwordRules(),
        ];
    }

    private function passwordRules(): array
    {
        $passwordHasher = new PasswordHasher();

        return [
            Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),

            function () use ($passwordHasher): Result {
                if (!$this->moduleSettings->getUserNameCaseSensitive()) {
                    $this->login = strtolower($this->login);
                }

                /** @var User|null $user */
                $user = $this->repositoryUser->findUserByUsernameOrEmail($this->login);

                $result = new Result();

                if ($user === null) {
                    $result->addError($this->translator->translate('Invalid login or password', [], 'user'));
                }

                if ($user !== null && $user->isBlocked()) {
                    $result->addError(
                        $this->translator->translate('Your user has been blocked, contact an administrator', [], 'user')
                    );
                }

                if ($user !== null && !$user->isConfirmed()) {
                    $result->addError(
                        $this->translator->translate('Please check your email to activate your account', [], 'user')
                    );
                }

                if ($user !== null && !$passwordHasher->validate($this->password, $user->getPasswordHash())) {
                    $result->addError($this->translator->translate('Invalid login or password', [], 'user'));
                }

                if ($result->isValid() && $user !== null) {
                    $this->lastLogout = $user->getLastLogout();
                    $user->updateAttributes(['ip_last_login' => $this->ip, 'last_login_at' => time()]);
                    $this->currentUser->login($user);
                }

                return $result;
            },
        ];
    }
}
