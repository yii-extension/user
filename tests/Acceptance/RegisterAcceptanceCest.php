<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class RegisterAcceptanceCest
{
    public function testRegisterPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->expectTo('see register page.');
        $I->seeInTitle('Register');
    }

    public function testRegisterSuccessDataDefaultAccountConfirmationFalse(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'administrator@example.com');
        $I->fillField('#register-username', 'admin');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->dontSeeLink('Register', '#form-registration-register');
    }

    public function testRegisterEmptyData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Value cannot be blank');
        $I->see('Value cannot be blank');
        $I->see('Value cannot be blank');
        $I->see('Register', '#form-registration-register');
    }

    public function testRegisterWrongEmailData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'register');
        $I->fillField('#register-username', 'register');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('This value is not a valid email address');
        $I->see('Register', '#form-registration-register');
    }

    public function testRegisterEmailExistData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'administrator@example.com');
        $I->fillField('#register-username', 'administrator');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Email already registered');
        $I->see('Register', '#form-registration-register');
    }

    public function testRegisterInvalidUsernameData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'demo@example.com');
        $I->fillField('#register-username', '**admin');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Value is invalid');

        $I->amOnPage('/register');

        $I->fillField('#register-email', 'demo@example.com');
        $I->fillField('#register-username', '**');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Username should contain at least 3 characters');
        $I->see('Register', '#form-registration-register');
    }

    public function testRegisterUsernameExistData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'demo@example.com');
        $I->fillField('#register-username', 'admin');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Username already registered');
        $I->see('Register', '#form-registration-register');
    }

    public function testRegisterInvalidPasswordData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'demo@example.com');
        $I->fillField('#register-username', 'demo');
        $I->fillField('#register-password', '123');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Password should contain at least 6 characters');
        $I->see('Register', '#form-registration-register');
    }
}
