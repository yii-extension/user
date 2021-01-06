<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\App\Asset;

use Composer\InstalledVersions;
use Yii\Extension\Fontawesome\Dev\Css\NpmAllAsset;
use Yiisoft\Assets\AssetBundle;
use Yiisoft\Yii\Bootstrap5\Assets\BootstrapAsset;
use Yiisoft\Yii\Bulma\Asset\BulmaAsset;
use Yiisoft\Yii\Bulma\Asset\BulmaHelpersAsset;
use Yiisoft\Yii\Bulma\Asset\BulmaJsAsset;

class AppAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@resources/assets/css';

    public array $css = [
        'site.css',
    ];

    public function __construct()
    {
        if (InstalledVersions::isInstalled('yii-extension/user-view-bootstrap5')) {
            $this->depends = [BootstrapAsset::class, BootstrapIconsAsset::class];
        }

        if (InstalledVersions::isInstalled('yii-extension/user-view-bulma')) {
            $this->depends = [BulmaAsset::class, BulmaHelpersAsset::class, BulmaJsAsset::class, NpmAllAsset::class];
        }
    }
}
