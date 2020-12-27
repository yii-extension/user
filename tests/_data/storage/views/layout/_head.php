<?php

declare(strict_types=1);

use Yii\Extension\Service\ServiceParameter;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var ServiceParameter $serviceParameter
 * @var string $csrf
 * @var Webview $this
 */

?>

<head>
    <meta charset=<?= $serviceParameter->get('app.charset') ?>>
    <meta http-equiv="X-UA-Compatible" content = "IE=edge">
    <meta name="viewport" content = "width=device-width, initial-scale=1">
    <meta name="csrf" content = <?= $csrf ?>>

    <title><?= Html::encode($this->getTitle()) ?></title>

    <?php $this->head() ?>
</head>
