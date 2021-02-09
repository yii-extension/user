<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\User\User;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;

/**
 * @var User $user
 * @var array $menuItems
 */

$config = [
    'brandLabel()' => ['My Project'],
    'brandOptions()' => [['encode' => false]],
    'collapseOptions()' => [['encode' => false]],
    'options()' => [
        ['class' => 'navbar-dark navbar-expand-lg bg-dark navbar-expand-lg navbar', 'encode' => false]
    ],
    'togglerOptions()' => [['encode' => false]],
];
$currentUri = $urlMatcher->getCurrentUri();
$currentUrl = '';
$menuItems = [
    [
        'label' => 'Login',
        'url' => $urlGenerator->generate('login'),
        'visible' => $user->isGuest(),
        'linkOptions' => ['encode' => false],
        'options' => ['encode' => false],
    ],
    [
        'label' => 'Email Change',
        'url' => $urlGenerator->generate('email/change'),
        'visible' => !$user->isGuest(),
        'linkOptions' => ['encode' => false],
        'options' => ['encode' => false],
    ],
    [
        'label' => 'Profile',
        'url' => $urlGenerator->generate('profile'),
        'visible' => !$user->isGuest(),
        'linkOptions' => ['encode' => false],
        'options' => ['encode' => false],
    ],
];

$menuItemsLogout = [];

if ($currentUri !== null) {
    $currentUrl = $currentUri->getPath();
}

if (!$user->isGuest()) {
    $menuItemsLogout = [
        [
            'label' => Form::widget()
                ->action($urlGenerator->generate('logout'))
                ->options(['csrf' => $csrf, 'encode' => false])
                ->begin() .
                    Html::submitButton(
                        'Logout (' . Html::encode($user->getIdentity()->getUsername()) . ')',
                        ['class' => 'btn btn-light btn-outline-dark', 'id' => 'logout', 'encode' => false],
                    ) .
                Form::end(),
            'encode' => false,
            'linkOptions' => ['encode' => false],
            'options' => ['encode' => false],
        ]
    ];
}
?>

<?= NavBar::widget($config)->begin() ?>

    <?= Nav::widget()
        ->currentPath($currentUrl)
        ->items($menuItems)
        ->options(['class' => 'navbar-nav ms-auto flex-nowrap', 'encode' => false]) ?>

    <?= Nav::widget()->items($menuItemsLogout)->options(['encode' => false]) ?>

<?= NavBar::end();
