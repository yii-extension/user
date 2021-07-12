<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Throwable;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryToken;
use Yii\Extension\User\Repository\RepositoryUser;
use Yii\Extension\User\Settings\ModuleSettings;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\InvalidArgumentException;
use Yiisoft\Db\Exception\StaleObjectException;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;

final class ServiceAttemptEmailChange
{
    private Flash $flash;
    private ModuleSettings $moduleSettings;
    private RepositoryToken $repositoryToken;
    private RepositoryUser $repositoryUser;
    private TranslatorInterface $translator;

    public function __construct(
        Flash $flash,
        ModuleSettings $moduleSettings,
        RepositoryToken $repositoryToken,
        RepositoryUser $repositoryUser,
        TranslatorInterface $translator
    ) {
        $this->flash = $flash;
        $this->moduleSettings = $moduleSettings;
        $this->repositoryToken = $repositoryToken;
        $this->repositoryUser = $repositoryUser;
        $this->translator = $translator;
    }

    /**
     * @throws Exception|InvalidArgumentException|StaleObjectException|Throwable
     */
    public function run(string $code, User $user): bool
    {
        $result = true;

        $emailChangeStrategy = $this->moduleSettings->getEmailChangeStrategy();
        $tokenConfirmWithin = $this->moduleSettings->getTokenConfirmWithin();
        $tokenRecoverWithin = $this->moduleSettings->getTokenRecoverWithin();

        /** @var Token|null $token */
        $token = $this->repositoryToken->findToken([
            'user_id' => $user->getId(),
            'code' => $code,
        ])->andWhere(['IN', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])->one();

        if ($token === null || $token->isExpired($tokenConfirmWithin, $tokenRecoverWithin)) {
            $message = $this->translator->translate('Your confirmation token is invalid or expired', [], 'user');
            $this->flash->add(
                'danger',
                [
                    'message' => $this->translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );

            $result = false;
        }

        if ($token !== null && $this->repositoryUser->findUserByEmail($user->getUnconfirmedEmail()) === null) {
            $token->delete();

            if ($emailChangeStrategy === User::STRATEGY_SECURE) {
                if ($token->getType() === Token::TYPE_CONFIRM_NEW_EMAIL) {
                    $user->flags |= User::NEW_EMAIL_CONFIRMED;
                }

                if ($token->getType() === Token::TYPE_CONFIRM_OLD_EMAIL) {
                    $user->flags |= User::OLD_EMAIL_CONFIRMED;
                }
            }

            if (
                $emailChangeStrategy === User::STRATEGY_DEFAULT ||
                ($user->flags & User::NEW_EMAIL_CONFIRMED) && ($user->flags & User::OLD_EMAIL_CONFIRMED)
            ) {
                $user->email($user->getUnconfirmedEmail());
                $user->unconfirmedEmail(null);
                $message = $this->translator->translate('Your email address has been changed', [], 'user');
                $this->flash->add(
                    'danger',
                    [
                        'message' => $this->translator->translate('System Notification', [], 'user') . PHP_EOL .
                            $message,
                    ],
                );
            }

            $result = $user->save();
        }

        return $result;
    }
}
