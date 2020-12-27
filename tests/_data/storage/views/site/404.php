<?php

use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var UrlMatcherInterface $urlMatcher
 */

$this->setTitle('404');

?>

<h1 class="is-size-1">
    <b>404</b>
</h1>

<p class="has-text-danger">
    The page
    <strong><?= Html::encode($urlMatcher->getCurrentUri()->getPath()) ?></strong>
    not found.
</p>

<p class="has-text-grey">
    The above error occurred while the Web server was processing your request.<br/>
    Please contact us if you think this is a server error. Thank you.
</p>

<hr class="mb-2">

<a class ="button is-danger mt-5" href="<?= $urlGenerator->generate('index') ?>">
    Go Back Home
</a>
