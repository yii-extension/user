<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class ResendAcceptanceCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('/resend');
    }

    public function testResendPage(AcceptanceTester $I): void
    {
        $I->expectTo('see resend page.');
        $I->see('Resend confirmation message.');
        $I->see('Please fill out the following.');
    }

    public function testResendEmptyDataTest(AcceptanceTester $I): void
    {
        $I->click('Continue', '#form-registration-resend');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank.');
    }

    public function testResendSubmitFormWrongData(AcceptanceTester $I): void
    {
        $I->fillField('#resend-email', 'noExist');

        $I->click('Continue', '#form-registration-resend');

        $I->expectTo('see validations errors.');
        $I->see('This value is not a valid email address.');

        $I->fillField('#resend-email', 'noExist@example.com');

        $I->click('Continue', '#form-registration-resend');

        $I->expectTo('see validations errors.');
        $I->see('Thank you. If said email is registered, you will get a password reset.');
    }

    public function testResendUserIsActive(AcceptanceTester $I): void
    {
        $I->fillField('#resend-email', 'administrator@example.com');

        $I->click('Continue', '#form-registration-resend');

        $I->expectTo('see validations errors.');
        $I->see('User is active.');
    }
}
