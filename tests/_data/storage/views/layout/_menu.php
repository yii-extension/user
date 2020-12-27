<?php

declare(strict_types=1);

use Yii\Extension\Service\ServiceParameter;
use Yiisoft\Yii\Bulma\Nav;
use Yiisoft\Yii\Bulma\NavBar;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlMatcherInterface;

/**
 * @var string $currentUrl
 * @var array $menuItems
 * @var ServiceParameter $serviceParameter
 * @var UrlMatcherInterface $urlMatcher
 */

$menuItems =  $serviceParameter->get('nav.guest');
$currentUrl = '';

if ($urlMatcher->getCurrentUri() !== null) {
    $currentUrl = $urlMatcher->getCurrentUri()->getPath();
}

?>

<?= NavBar::widget($serviceParameter->get('navBar.config'))->begin() ?>

    <?= Nav::widget()
        ->currentPath($currentUrl)
        ->items($menuItems)
    ?>

<?= NavBar::end();
