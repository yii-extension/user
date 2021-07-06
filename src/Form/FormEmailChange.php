<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\Simple\Model\BaseModel;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;

final class FormEmailChange extends BaseModel
{
    private string $email = '';
    private string $oldEmail = '';
    private RepositoryUser $repositoryUser;
    private ModuleSettings $moduleSettings;
    private TranslatorInterface $translator;
    private User $user;

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(
        CurrentUser $currentUser,
        ModuleSettings $moduleSettings,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator
    ) {
        $this->repositoryUser = $repositoryUser;
        $this->moduleSettings = $moduleSettings;
        $this->translator = $translator;
        $this->user = $currentUser->getIdentity();
        $this->loadData();

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'email' => $this->translator->translate('Email', [], 'user'),
        ];
    }

    public function getFormName(): string
    {
        return 'EmailChange';
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRules(): array
    {
        return [
            'email' => $this->emailRules(),
        ];
    }

    private function loadData(): void
    {
        $this->email = $this->user->getEmail();

        if ($this->user->getUnconfirmedEmail() !== '') {
            $this->email = $this->user->getUnconfirmedEmail();
            $this->addError(
                'email',
                $this->translator->translate('Please check your email to confirm the change', [], 'user'),
            );
        }
        $this->oldEmail = $this->user->getEmail();
    }

    private function emailRules(): array
    {
        return [
            Required::rule()->message($this->translator->translate('Value cannot be blank', [], 'user')),
            Email::rule()->message($this->translator->translate('This value is not a valid email address', [], 'user')),

            function (): Result {
                $result = new Result();

                $user = $this->repositoryUser->findUserByUsernameOrEmail($this->email);

                if ($user && $this->email !== $this->user->getEmail()) {
                    $result->addError($this->translator->translate('Email already registered', [], 'user'));
                }

                return $result;
            },
        ];
    }
}
