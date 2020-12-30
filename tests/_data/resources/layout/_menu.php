<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bulma\Nav;
use Yiisoft\Yii\Bulma\NavBar;

/** @var App\ApplicationParameters $applicationParameters */
/** @var Yiisoft\Router\UrlMatcherInterface $urlMatcher */


$currentUrl = '';
$currentUri = $urlMatcher->getCurrentUri();
$menuItems = [];

if ($currentUri !== null) {
    $currentUrl = $currentUri->getPath();
}

if (!$user->isGuest()) {
    $menuItems = [
        [
            'label' => Form::widget()
                ->action($urlGenerator->generate('logout'))
                ->options(['csrf' => $csrf])
                ->begin() .
                    Html::submitButton(
                        'Logout (' . Html::encode($user->getIdentity()->getUsername()) . ')',
                        ['class' => 'button is-black is-inverted', 'id' => 'logout'],
                    ) .
                Form::end(),
            'encode' => false
        ]
    ];
}

?>

<?= NavBar::widget()
    ->brandLabel($applicationParameters->getName())
    ->brandImage('/images/yii-logo.jpg')
    ->options(['class' => 'is-black', 'data-sticky' => '', 'data-sticky-shadow' => ''])
    ->itemsOptions(['class' => 'navbar-end'])
    ->begin()
?>

    <?= Nav::widget()
        ->currentPath($currentUrl)
        ->items($menuItems)
    ?>

<?= NavBar::end();

