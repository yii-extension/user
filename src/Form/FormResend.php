<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormResend extends FormModel
{
    private string $email = '';
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;

    public function __construct(
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->repositoryUser = $repositoryUser;
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'email' => $this->translator->translate('Email'),
        ];
    }

    public function formName(): string
    {
        return 'Resend';
    }

    public function getEmail(): string
    {
        return strtolower($this->email);
    }

    public function rules(): array
    {
        $email = new email();
        $required = new Required();

        return [
            'email' => $this->emailRules(),
        ];
    }

    private function emailRules(): array
    {
        $email = new email();
        $required = new Required();

        return [
            $required->message($this->translator->translate('Value cannot be blank')),
            $email->message($this->translator->translate('This value is not a valid email address')),

            function (): Result {

                $result = new Result();

                /** @var User|null $user */
                $user = $this->repositoryUser->findUserByUsernameOrEmail($this->email);

                if ($user === null) {
                    $result->addError(
                        $this->translator->translate('Thank you. If said email is registered, you will get a password reset')
                    );
                }

                if ($user !== null && $user->isConfirmed()) {
                    $result->addError($this->translator->translate('User is active'));
                }

                return $result;
            }
        ];
    }
}
