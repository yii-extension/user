<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bulma\Nav;
use Yiisoft\Yii\Bulma\NavBar;

/**
 * @var Yiisoft\Router\UrlMatcherInterface $urlMatcher
 */

$config = [
    'brandLabel()' => ['My Project'],
    'brandImage()' => ['/images/yii-logo.jpg'],
    'brandImageOptions()' => [['encode' => false]],
    'options()' => [['class' => 'is-black']],
    'itemsOptions()' => [['class' => 'navbar-end']],
];
$currentUrl = '';
$currentUri = $urlMatcher->getCurrentUri();
$menuItems = [
    [
        'label' => 'Login',
        'url' => $urlGenerator->generate('login'),
        'visible' => $user->isGuest(),
        'linkOptions' => ['encode' => false],
    ],
    [
        'label' => 'Email Change',
        'url' => $urlGenerator->generate('email/change'),
        'visible' => !$user->isGuest(),
        'linkOptions' => ['encode' => false],
    ],
    [
        'label' => 'Profile',
        'url' => $urlGenerator->generate('profile'),
        'visible' => !$user->isGuest(),
        'linkOptions' => ['encode' => false],
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
                        html::tag('strong', 'Logout (' . Html::encode($user->getIdentity()->getUsername()) . ')'),
                        ['class' => 'button is-outlined is-rounded is-small', 'id' => 'logout', 'encode' => false],
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

    <?= Nav::widget()->currentPath($currentUrl)->items($menuItems) ?>

    <?= Nav::widget()->items($menuItemsLogout) ?>

<?= NavBar::end();

