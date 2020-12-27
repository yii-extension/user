<?php

declare(strict_types=1);

use Yii\Extension\Service\ServiceParameter;

/**
 * @var ServiceParameter $serviceParameter
 */

$this->params['breadcrumbs'] = '/';

$this->setTitle($serviceParameter->get('name'));

?>

<h1 class="title">Hello World</h1>
<p class="subtitle">My first website with <strong>Yii 3.0</strong>!</p>
