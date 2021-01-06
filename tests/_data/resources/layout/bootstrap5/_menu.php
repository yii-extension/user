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
    'collapseOptions()' => [[]],
    'options()' => [['class' => 'navbar-dark navbar-expand-lg bg-dark navbar-expand-lg navbar']],
];
$currentUri = $urlMatcher->getCurrentUri();
$currentUrl = '';
$menuItems = [
    [
        'label' => 'Login',
        'url' => $urlGenerator->generate('login'),
        'visible' => $user->isGuest(),
    ],
    [
        'label' => 'Profile',
        'url' => $urlGenerator->generate('profile'),
        'visible' => !$user->isGuest(),
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
                ->options(['csrf' => $csrf])
                ->begin() .
                    Html::submitButton(
                        'Logout (' . Html::encode($user->getIdentity()->getUsername()) . ')',
                        ['class' => 'btn btn-light btn-outline-dark', 'id' => 'logout'],
                    ) .
                Form::end(),
            'encode' => false
        ]
    ];
}
?>

<?= NavBar::widget($config)->begin() ?>

    <?= Nav::widget()
        ->currentPath($currentUrl)
        ->items($menuItems)
        ->options(['class' => 'navbar-nav ms-auto flex-nowrap']) ?>

    <?= Nav::widget()->items($menuItemsLogout) ?>

<?= NavBar::end();
