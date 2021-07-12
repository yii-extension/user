<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Yiisoft\Session\Flash\Flash;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryToken;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;

final class ServiceSecureEmailChange
{
    private Flash $flash;
    private MailerUser $mailerUser;
    private RepositoryToken $repositoryToken;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        Flash $flash,
        MailerUser $mailerUser,
        RepositoryToken $repositoryToken,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->flash = $flash;
        $this->mailerUser = $mailerUser;
        $this->repositoryToken = $repositoryToken;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function run(User $user): void
    {
        $result = $this->repositoryToken->register($user->getId(), Token::TYPE_CONFIRM_OLD_EMAIL);
        $email = $user->getEmail();

        /** @var Token|null $token */
        $token = $this->repositoryToken->findTokenByCondition(
            ['user_id' => $user->getId(), 'type' => Token::TYPE_CONFIRM_OLD_EMAIL]
        );

        if ($result && $token !== null) {
            $params = [
                'username' => $user->getUsername(),
                'url' => $this->urlGenerator->generateAbsolute(
                    $token->toUrl(),
                    ['id' => $token->getUserId(), 'code' => $token->getCode()]
                ),
            ];

            if ($this->mailerUser->sendReconfirmationMessage($email, $params)) {
                $message = $this->translator->translate(
                    'We have sent confirmation links to both old email: {email} and new email: {newEmail} addresses.' .
                    ' You must click both links to complete your request',
                    ['email' => $user->getEmail(), 'newEmail' => $user->getUnconfirmedEmail()],
                    'user',
                );
                $this->flash->add(
                    'info',
                    [
                        'message' => $this->translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                    ],
                );
            }

            // unset flags if they exist
            $user->flags &= ~User::NEW_EMAIL_CONFIRMED;
            $user->flags &= ~User::OLD_EMAIL_CONFIRMED;

            $user->update();
        }
    }
}
