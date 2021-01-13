<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Translator\Translator;

/**
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UrlMatcherInterface $urlMatcher
 */

$this->setTitle('404');
?>

<h1 class="fw-bold text-center">404</h1>

<div class="text-center">
    <p class="text-danger">
        <?= $translator->translate(
            'The page {url} was not found',
            ['url' => Html::tag('strong', Html::encode($urlMatcher->getCurrentUri()->getPath()))],
            'user-view',
        ); ?>.
    </p>

    <p>
        <?= $translator->translate(
            'The above error occurred while the Web server was processing your request',
            [],
            'user-view'
        ) ?>.
        <br/>
        <?= $translator->translate('Please contact us if you think this is a server error. Thank you', [], 'user-view') ?>.
    </p>

    <hr class="mt-3">

    <a class ="btn btn-danger" href="<?= $urlGenerator->generate('home') ?>">
        <?= $translator->translate('Go Back Home', [], 'user-view') ?>
    </a>
</div>
