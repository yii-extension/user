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

        $I->expectTo('registration resend options default [accountConfirmation = false].');
        $I->see('404');
        $I->see('The page /resend was not found.');
    }
}
