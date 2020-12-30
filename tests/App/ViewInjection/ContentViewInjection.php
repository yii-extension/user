<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\App\ViewInjection;

use Yii\Extension\User\Tests\App\ApplicationParameters;
use Yiisoft\Yii\View\ContentParametersInjectionInterface;

final class ContentViewInjection implements ContentParametersInjectionInterface
{
    private ApplicationParameters $applicationParameters;

    public function __construct(
        ApplicationParameters $applicationParameters
    ) {
        $this->applicationParameters = $applicationParameters;
    }

    public function getContentParameters(): array
    {
        return [
            'applicationParameters' => $this->applicationParameters,
        ];
    }
}
