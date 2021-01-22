<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class AccountAcceptanceCest
{
    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testAccountPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the account page');
        $I->amOnPage('/account');
        $I->see('Account setting');
    }

    public function testAccountEmailValidation(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the account page');
        $I->amOnPage('/account');
        $I->see('Account setting');

        $I->fillField('#account-email', '');

        $I->click('Save', '#form-setting-account');

        $I->expectTo('see register validation.');
        $I->see('Value cannot be blank');

        $I->fillField('#account-email', 'noexist');

        $I->click('Save', '#form-setting-account');

        $I->expectTo('see register validation.');
        $I->see('This value is not a valid email address');
    }
}
