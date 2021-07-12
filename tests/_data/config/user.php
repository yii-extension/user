<?php

declare(strict_types=1);

namespace Yii\Component;

use Yii\Extension\User\Settings\ModuleSettings;
use Yii\Extension\User\Tests\App\ActiveRecord\SettingsTest;
use Yiisoft\ActiveRecord\ActiveRecordFactory;

return [
    /** Config yii-extension-user */
    ModuleSettings::class => static fn (ActiveRecordFactory $activeRecordFactory) => $activeRecordFactory
        ->createQueryTo(SettingsTest::class)->findOne(1),
];
