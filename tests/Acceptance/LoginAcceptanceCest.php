<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class LoginAcceptanceCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');
    }

    public function testAuthLoginPage(AcceptanceTester $I): void
    {
        $I->expectTo('see login page.');
        $I->see('Sing in.');
        $I->see('Please fill out the following.');
    }

    public function testAuthLoginEmptyDataTest(AcceptanceTester $I): void
    {
        $I->click('Login', '#form-security-login');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank.');
        $I->see('Value cannot be blank.');
        $I->see('Login', '#form-security-login');
    }

    public function testAuthLoginSubmitFormWrongData(AcceptanceTester $I): void
    {
        $I->fillField('#login-login', 'admin1');
        $I->fillField('#login-password', '1234567');

        $I->click('Login', '#form-security-login');

        $I->expectTo('see validations errors.');
        $I->see('Unregistered user/Invalid password.');
        $I->see('Login', '#form-security-login');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testAuthLoginUsernameSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Login', '#form-security-login');

        $I->expectTo('see logged index page.');
        $I->see('Logout (admin)');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testAuthLoginEmailSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->fillField('#login-login', 'administrator@example.com');
        $I->fillField('#login-password', '123456');

        $I->click('Login', '#form-security-login');

        $I->expectTo('see logged index page.');
        $I->see('Logout (admin)');
    }

    public function testAuthLoginSettingsPasswordRecoveryTrue(AcceptanceTester $I): void
    {
        $I->expectTo('see link forgot password');
        $I->see('Forgot Password');
    }
}
