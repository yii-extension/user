<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class LoginAcceptanceCest
{
    public function testLoginPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see login page.');
        $I->seeInTitle('Login');
    }

    public function testLoginEmptyDataTest(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->click('Login', '#form-auth-login');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank.');
        $I->see('Value cannot be blank.');
        $I->see('Login', '#form-auth-login');
    }

    public function testLoginSubmitFormWrongData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin1');
        $I->fillField('#login-password', '1234567');

        $I->click('Login', '#form-auth-login');

        $I->expectTo('see validations errors.');
        $I->see('Unregistered user/Invalid password.');
        $I->see('Login', '#form-auth-login');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testLoginUsernameSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Login', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('no see page login');
        $I->see('404');
        $I->see('The page /login was not found.');

        $I->click('#logout');

        $I->expectTo('no see link logout');
        $I->dontSeeLink('logout');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testLoginEmailSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'administrator@example.com');
        $I->fillField('#login-password', '123456');

        $I->click('Login', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('no see page login');
        $I->see('404');
        $I->see('The page /login was not found.');

        $I->click('#logout');

        $I->expectTo('no see link logout');
        $I->dontSeeLink('logout');
    }

    public function testLoginSettingsPasswordRecoveryTrue(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see link forgot password');
        $I->see('Forgot password');
    }

    public function testLoginSettingsRegisterTrue(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see link register');
        $I->see("Don't have an account - Sign up!");
    }
}
