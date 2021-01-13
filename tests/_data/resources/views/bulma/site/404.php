<?php

use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Translator\Translator;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var UrlMatcherInterface $urlMatcher
 * @var Translator $translator
 */

$title = Html::encode('404');

/** @psalm-suppress InvalidScope */
$this->setTitle($title);
?>

<div class="has-text-centered">
    <h1 class="is-size-1">
        <b><?= $title ?></b>
    </h1>

    <p class="has-text-danger">
        <?= $translator->translate(
            'The page {url} was not found',
            ['url' => Html::tag('strong', Html::encode($urlMatcher->getCurrentUri()->getPath()))],
            'user-view',
        ); ?>.
    </p>

    <p class="has-text-grey">
        <?= Html::encode(
            $translator->translate(
                'The above error occurred while the Web server was processing your request',
                [],
                'user-view',
            )
        ) ?>.
        <br/>
        <?= Html::encode(
            $translator->translate('Please contact us if you think this is a server error. Thank you', [], 'user-view')
        ) ?>.
    </p>

    <hr class="mb-2">

    <a class ="button is-danger mt-5" href="<?= $urlGenerator->generate('home') ?>">
        <?= Html::encode($translator->translate('Go Back Home', [], 'user-view')) ?>
    </a>
</div>
