<?php

declare(strict_types=1);

use Yii\Extension\User\Tests\App\Asset\AppAsset;
use Yii\Extension\Widget\FlashMessage;
use Yiisoft\Html\Html;

/**
 * @var App\ApplicationParameters $applicationParameters
 * @var Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 * @var string|null $csrf
 * @var Yiisoft\View\WebView $this
 * @var Yiisoft\Router\UrlMatcherInterface $urlMatcher
 */

$assetManager->register([
    AppAsset::class,
]);

$this->setCssFiles($assetManager->getCssFiles());
$this->setJsFiles($assetManager->getJsFiles());
$this->setJsStrings($assetManager->getJsStrings());
$this->setJsVar($assetManager->getJsVar());
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>

    <html class="h-100" lang="en">

    <?= $this->render('_head', ['applicationParameters' => $applicationParameters]) ?>

    <?php $this->beginBody() ?>
        <body>
            <section class="hero is-fullheight is-light">
                <div class="hero-head has-background-black">
                    <?= $this->render(
                        '_menu',
                        [
                            'applicationParameters' => $applicationParameters,
                            'csrf' => $csrf,
                            'urlGenerator' => $urlGenerator,
                            'urlMatcher' => $urlMatcher,
                            'user' => $user,
                        ]
                    ) ?>
                </div>
                <div>
                    <?= FlashMessage::widget() ?>
                </div>
                <div class="hero-body is-light">
                    <div class="container">
                        <?= $content ?>
                    </div>
                </div>
                <div class="hero-footer has-background-black">
                    <?= $this->render('_footer', ['applicationParameters' => $applicationParameters]) ?>
                </div>
            </section>
        </body>
    <?php $this->endBody() ?>

</html>

<?php $this->endPage() ?>
