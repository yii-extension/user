<?php

declare(strict_types=1);

namespace Yii\Extension\User\Form;

use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryUser;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidatorFactoryInterface;

use function strtolower;

final class FormRequest extends FormModel
{
    private string $email = '';
    private ?string $userId = '';
    private string $username = '';
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;
    private Token $token;

    public function __construct(
        Token $token,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator,
        ValidatorFactoryInterface $validatorFactory
    ) {
        $this->token = $token;
        $this->repositoryUser = $repositoryUser;
        $this->translator = $translator;

        parent::__construct($validatorFactory);
    }

    public function attributeLabels(): array
    {
        return [
            'email' => $this->translator->translate('Email', [], 'user'),
        ];
    }

    public function formName(): string
    {
        return 'Request';
    }

    public function getEmail(): string
    {
        return strtolower($this->email);
    }

    public function rules(): array
    {
        return [
            'email' => $this->emailRules(),
        ];
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    private function emailRules(): array
    {
        $email = new email();
        $required = new Required();

        return [
            $required->message($this->translator->translate('Value cannot be blank', [], 'user')),
            $email->message($this->translator->translate('This value is not a valid email address', [], 'user')),

            function (): Result {

                $result = new Result();

                /** @var User|null $user */
                $user = $this->repositoryUser->findUserByUsernameOrEmail($this->email);

                if ($user === null) {
                    $result->addError(
                        $this->translator->translate(
                            'Thank you. If said email is registered, you will get a password reset',
                            [],
                            'user',
                        )
                    );
                }

                if ($user !== null && !$user->isConfirmed()) {
                    $result->addError($this->translator->translate('Inactive user', [], 'user'));
                }

                if ($result->isValid()) {
                    $this->userId = $user->getId();
                    $this->username = $user->getUsername();

                    $this->token->deleteAll(['user_id' => $this->userId, 'type' => Token::TYPE_RECOVERY]);
                }

                return $result;
            }
        ];
    }
}
