<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class ResendAcceptanceCest
{
    public function testRegistrationResendDefaultAccountConfirmationFalse(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->wantTo('registration resend options default [accountConfirmation = false].');

        $I->expectTo('see registration resend validation.');
        $I->amOnPage('/resend');

        $I->see('The page /resend not found.');
    }
}
