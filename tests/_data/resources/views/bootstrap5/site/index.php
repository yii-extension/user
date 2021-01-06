<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

$title = 'My Project';

$this->setTitle(Html::encode($title));
?>

<div class="jumbotron jumbotron-fluid text-center">

    <h1 class="title">Hello!</h1>

    <p class="subtitle">Let's start something great with <strong>Yii3</strong>!</p>

    <p class="subtitle">
        <a href="https://github.com/yiisoft/docs/tree/master/guide/en" target="_blank" rel="noopener">
            Don't forget to check the guide.
        </a>
    </p>

</div>
