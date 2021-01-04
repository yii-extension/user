<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class RequestAcceptanceCest
{
    public function testRequestPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/request');

        $I->expectTo('see login page.');
        $I->see('Reset password');
    }

    public function testRequestEmptyDataTest(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/request');

        $I->fillField('#request-email', '');

        $I->click('Continue', '#form-recovery-request');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank.');
    }

    public function testRequestSubmitFormWrongData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/request');

        $I->fillField('#request-email', 'noExist');

        $I->click('Continue', '#form-recovery-request');

        $I->expectTo('see validations errors.');
        $I->see('This value is not a valid email address.');

        $I->fillField('#request-email', 'noexist@mail.com');

        $I->click('Continue', '#form-recovery-request');

        $I->expectTo('see validations errors.');
        $I->see('Email not registered.');
    }

    public function testRequestSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/request');

        $I->fillField('#request-email', 'administrator@example.com');

        $I->click('Continue', '#form-recovery-request');

        $I->expectTo('see registration register validation.');
        $I->dontSeeLink('Request Password', '#form-recovery-request');
    }
}
