<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class ResendAcceptanceCest
{
    public function testResendDefaultAccountConfirmationFalse(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->expectTo('error 404 response');
        $I->seeResponseCodeIs(404);
    }
}
