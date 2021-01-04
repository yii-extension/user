<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Form\FormLogin;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\User\User as IdentityUser;

final class ServiceLogin
{
    private FormLogin $formLogin;
    private IdentityUser $identityUser;

    public function __construct(FormLogin $formLogin, IdentityUser $identityUser)
    {
        $this->formLogin = $formLogin;
        $this->identityUser = $identityUser;
    }

    public function run(RepositoryUser $repositoryUser, string $ip): bool
    {
        $login = $this->formLogin->getLogin();
        $password = $this->formLogin->getPassword();
        $remember = $this->formLogin->getRemember();

        /** @var User $user */
        $user = $repositoryUser->findUserByUsernameOrEmail($login);

        if ($user === null) {
            $this->formLogin->addError('password', 'Unregistered user/Invalid password.');
        } elseif ($user->isBlocked()) {
            $this->formLogin->addError('password', 'Your user has been blocked, contact an administrator.');
        }

        if (
            $user &&
            !$user->isBlocked() &&
            $repositoryUser->validatePassword(
                $this->formLogin,
                $password,
                $user->getAttribute('password_hash')
            ) &&
            $this->validateConfirmed($user)
        ) {
            $this->updateAttributeLogin($user, $ip);

            /** @var IdentityInterface $user */
            $result = $this->identityUser->login($user);
        } else {
            $this->formLogin->addError('password', 'Unregistered user/Invalid password.');
            $result = false;
        }

        return $result;
    }

    public function isLoginConfirm(User $user, string $ip): bool
    {
        $this->updateAttributeLogin($user, $ip);

        return $this->identityUser->login($user);
    }

    private function updateAttributeLogin(User $user, string $ip): void
    {
        $user->updateAttributes(['ip_last_login' => $ip, 'last_login_at' => time()]);
    }

    private function validateConfirmed(User $user): bool
    {
        $result = true;

        if ($user->getAttribute('confirmed_at') === null) {
            $this->formLogin->addError('password', 'Please check your email to activate your account.');
            $result = false;
        }

        return $result;
    }
}
