<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var UrlMatcherInterface $urlMatcher
 */

$this->setTitle('404');
?>

<h1 class="fw-bold">404</h1>

<p class="text-danger">
    <?= sprintf('The page %s was not found.', Html::tag('strong', Html::encode($urlMatcher->getCurrentUri()->getPath()))) ?>
</p>

<p>
    The above error occurred while the Web server was processing your request.
    <br/>
    Please contact us if you think this is a server error. Thank you.
</p>

<hr class="mb-3">

<a class ="btn btn-danger" href="<?= $urlGenerator->generate('index') ?>">
   Go Back Home
</a>
