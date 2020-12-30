<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class LoginFunctionalCest
{
    public function testAuthLoginSettingsPasswordRecoveryFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings password recovery false');
        $I->updateInDatabase('settings', ['passwordRecovery' => false], ['id' => 1]);

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('dont see link forgot password');
        $I->dontSeeLink('Forgot Password');

        $I->updateInDatabase('settings', ['passwordRecovery' => true], ['id' => 1]);
    }

    public function testAuthLoginSettingsUserNameCaseSensitiveDefault(FunctionalTester $I): void
    {
        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');

        $I->expectTo('see registration register form.');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'TestMe@example.com',
            'Register[username]' => 'TestMe',
            'Register[password]' => '123456'

        ]);

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see login form.');
        $I->submitForm('#form-security-login', [
            'Login[login]' => 'testme',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see error login form.');
        $I->see('Unregistered user/Invalid password.');

        $I->expectTo('see login form case sensitive.');
        $I->submitForm('#form-security-login', [
            'Login[login]' => 'TestMe',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see login sucess.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (TestMe)');
    }

    public function testAuthLoginSettingsUserNameCaseSensitiveFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings password recovery false');
        $I->updateInDatabase('settings', ['userNameCaseSensitive' => false], ['id' => 1]);

        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');

        $I->expectTo('see registration register form.');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'TestMe1@example.com',
            'Register[username]' => 'TestMe1',
            'Register[password]' => '123456'

        ]);

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see login form.');
        $I->submitForm('#form-security-login', [
            'Login[login]' => 'testme1',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see login sucess.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (testme1)');

        $I->amGoingTo('update settings password recovery true');
        $I->updateInDatabase('settings', ['userNameCaseSensitive' => true], ['id' => 1]);
    }
}
