<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\RepositorySetting;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;

final class FormEmailChange extends FormModel
{
    private string $email = '';
    private string $oldEmail = '';
    private CurrentUser $currentUser;
    private RepositorySetting $repositorySetting;
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(
        CurrentUser $currentUser,
        RepositorySetting $repositorySetting,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator
    ) {
        $this->currentUser = $currentUser->getIdentity();
        $this->repositorySetting = $repositorySetting;
        $this->repositoryUser = $repositoryUser;
        $this->translator = $translator;
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
        $this->email = $this->currentUser->getEmail();

        if ($this->currentUser->getUnconfirmedEmail() !== '') {
            $this->email = $this->currentUser->getUnconfirmedEmail();
            $this->addError(
                'email',
                $this->translator->translate('Please check your email to confirm the change', [], 'user'),
            );
        }
        $this->oldEmail = $this->currentUser->getEmail();
    }

    private function emailRules(): array
    {
        $email = new Email();
        $required = new Required();

        return [
            $required->message($this->translator->translate('Value cannot be blank', [], 'user')),
            $email->message($this->translator->translate('This value is not a valid email address', [], 'user')),

            function (): Result {
                $result = new Result();

                $user = $this->repositoryUser->findUserByUsernameOrEmail($this->email);

                if ($user && $this->email !== $this->currentUser->getEmail()) {
                    $result->addError($this->translator->translate('Email already registered', [], 'user'));
                }

                return $result;
            },
        ];
    }
}
