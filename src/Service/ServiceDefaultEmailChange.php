<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\Repository\RepositoryToken;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;

final class ServiceDefaultEmailChange
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

    public function run(string $email, User $user, bool $flash = true): void
    {
        $user->unconfirmedEmail($email);
        $this->repositoryToken->register($user->getId(), Token::TYPE_CONFIRM_NEW_EMAIL);

        /** @var Token $token */
        $token = $this->repositoryToken->findTokenByCondition(
            ['user_id' => $user->getId(), 'type' => Token::TYPE_CONFIRM_NEW_EMAIL]
        );
        $email = $user->getUnconfirmedEmail();
        $result = (bool) $user->update();

        if ($result) {
            $params = [
                'username' => $user->getUsername(),
                'url' => $this->urlGenerator->generateAbsolute(
                    $token->toUrl(),
                    ['id' => $token->getUserId(), 'code' => $token->getCode()]
                ),
            ];

            if ($this->mailerUser->sendReconfirmationMessage($email, $params) && $flash === true) {
                $message = $this->translator->translate(
                    'A confirmation message has been sent to your new email address {email}',
                    ['email' => $user->getEmail()],
                    'user',
                );
                $this->flash->add(
                    'info',
                    [
                        'message' => $this->translator->translate('System Notification', [], 'user') . PHP_EOL .
                            $message,
                    ],
                );
            }
        }
    }
}
