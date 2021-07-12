<?php

declare(strict_types=1);

namespace Yii\Extension\User\Service;

use Yiisoft\Session\Flash\Flash;
use Yii\Extension\User\ActiveRecord\User;
use Yiisoft\Translator\TranslatorInterface;

final class ServiceInsecureEmailChange
{
    private Flash $flash;
    private TranslatorInterface $translator;

    public function __construct(Flash $flash, TranslatorInterface $translator)
    {
        $this->flash = $flash;
        $this->translator = $translator;
    }

    public function run(string $email, User $user): void
    {
        $user->email($email);

        $result = (bool) $user->update();

        if ($result) {
            $message = $this->translator->translate('Your email address has been changed', [], 'user');
            $this->flash->add(
                'success',
                [
                    'message' => $this->translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );
        }
    }
}
