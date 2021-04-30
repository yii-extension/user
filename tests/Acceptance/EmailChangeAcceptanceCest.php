<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class EmailChangeAcceptanceCest
{
    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testEmailChangePage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello World');
        $I->see("My first website with Yii 3.0!");

        $I->amGoingTo('go to the email change page.');
        $I->amOnPage('/email/change');
        $I->see('Change email address');
    }

    public function testEmailChangeValidation(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello World');
        $I->see("My first website with Yii 3.0!");

        $I->amGoingTo('go to the email change page.');
        $I->amOnPage('/email/change');
        $I->see('Change email address');

        $I->fillField('#emailchange-email', '');

        $I->click('Save', '#form-email-change');

        $I->expectTo('see register validation.');
        $I->see('Value cannot be blank');

        $I->fillField('#emailchange-email', 'noexist');

        $I->click('Save', '#form-email-change');

        $I->expectTo('see register validation.');
        $I->see('This value is not a valid email address');
    }
}
