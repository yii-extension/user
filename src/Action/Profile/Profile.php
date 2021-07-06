<?php

declare(strict_types=1);

namespace Yii\Extension\User\Action\Profile;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii\Extension\User\Form\FormProfile;
use Yii\Extension\User\Repository\RepositoryProfile;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Profile
{
    public function run(
        CurrentUser $user,
        Flash $flash,
        FormProfile $formProfile,
        RepositoryProfile $repositoryProfile,
        ServerRequestInterface $serverRequest,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        ViewRenderer $viewRenderer
    ): ResponseInterface {
        /** @var array $body */
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();

        $id = $user->getId();

        if ($id !== null) {
            $repositoryProfile->loadData($id, $formProfile);
        }

        if (
            $method === 'POST' &&
            $id !== null &&
            $formProfile->load($body) &&
            $validator->validate($formProfile)->isValid() &&
            $repositoryProfile->update($id, $formProfile)
        ) {
            $message = $translator->translate('Your data has been saved', [], 'user');
            $flash->add(
                'success',
                [
                    'message' => $translator->translate('System Notification', [], 'user') . PHP_EOL . $message,
                ],
            );
        }

        return $viewRenderer
            ->withViewPath('@user-view-views')
            ->render('profile/profile', ['body' => $body, 'data' => $formProfile]);
    }
}
