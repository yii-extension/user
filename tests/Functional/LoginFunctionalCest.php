<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class LoginFunctionalCest
{
    public function testLoginSettingsPasswordRecoveryFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings password recovery false.');
        $I->updateInDatabase('settings', ['passwordRecovery' => false], ['id' => 1]);

        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('dont see link forgot password.');
        $I->dontSeeLink('Forgot Password');

        $I->amGoingTo('update settings password recovery true.');
        $I->updateInDatabase('settings', ['passwordRecovery' => true], ['id' => 1]);
    }

    public function testLoginSettingsRegisterFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings register false.');
        $I->updateInDatabase('settings', ['register' => false], ['id' => 1]);

        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('dont see link register.');
        $I->dontSeeLink("Don't have an account - Sign up!");

        $I->amGoingTo('update settings register true.');
        $I->updateInDatabase('settings', ['register' => true], ['id' => 1]);
    }

    public function testLoginSettingsUserNameCaseSensitiveDefault(FunctionalTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->expectTo('see registration register form.');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'TestMe@example.com',
            'Register[username]' => 'TestMe',
            'Register[password]' => '123456',
        ]);

        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->expectTo('see log in form.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'testme',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see error log in form.');
        $I->see('Invalid login or password');

        $I->expectTo('see log in form case sensitive.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'TestMe',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see log in sucess.');
        $I->see('Hello World');
        $I->see('My first website with Yii 3.0!');
    }

    public function testLoginSettingsUserNameCaseSensitiveFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings password recovery false.');
        $I->updateInDatabase('settings', ['userNameCaseSensitive' => false], ['id' => 1]);

        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->expectTo('see registration register form.');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'TestMe1@example.com',
            'Register[username]' => 'TestMe1',
            'Register[password]' => '123456',
        ]);

        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('see log in form.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'testme1',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see log in sucess.');
        $I->see('Hello World');
        $I->see('My first website with Yii 3.0!');

        $I->amGoingTo('update settings password recovery true.');
        $I->updateInDatabase('settings', ['userNameCaseSensitive' => true], ['id' => 1]);
    }

    public function testLoginSubmitFormWrongDataUsername(FunctionalTester $I): void
    {
        $I->amGoingTo('go to the login page.');
        $I->amOnPage('/login');

        $I->expectTo('see login form.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'admin1',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see validations errors.');
        $I->see('Invalid login or password');
        $I->see('Log in', '#form-auth-login');
    }

    public function testLoginSubmitFormWrongDataPassword(FunctionalTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('see log in form.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'admin',
            'Login[password]' => '1',
        ]);

        $I->expectTo('see validations errors.');
        $I->see('Invalid login or password');
        $I->see('Log in', '#form-auth-login');
    }

    public function testLoginUserIsBlocked(FunctionalTester $I): void
    {
        $I->amGoingTo('load fixture user confirmed.');
        $this->userConfirmed($I);

        $I->amGoingTo('block user test.');
        $I->updateInDatabase('user', ['blocked_at' => time()], ['id' => 100]);

        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('see log in form.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'alex@example.com',
            'Login[password]' => '123456',
        ]);

        $I->expectTo('see log in validation.');
        $I->see('Your user has been blocked, contact an administrator');

        $I->amGoingTo('unblock user test.');
        $I->updateInDatabase('user', ['blocked_at' => null], ['id' => 100]);
    }

    private function userConfirmed(FunctionalTester $I): void
    {
        $time = time();

        $I->haveInDatabase(
            'user',
            [
                'id' => 100,
                'username' => 'alex',
                'email' => 'alex@example.com',
                'password_hash' => '$argon2i$v=19$m=65536,t=4,p=1$ZVlUZk1NS2wwdi45d0t6dw$pn/0BLB3EzYtNdm3NSj6Yntk9lUT1pEOFsd85xV3Ig4',
                'auth_key' => 'zQh1A65We0AmHPOMiWN0WB0M90Q24ziU',
                'created_at' => $time,
                'updated_at' => $time,
                'confirmed_at' => $time,
                'blocked_at' => null,
            ]
        );

        $I->haveInDatabase(
            'token',
            [
                'user_id' => 100,
                'code' => '6f5d0dad53ef73e6ba6f01a441c0e602',
                'type' => 1,
                'created_at' => $time,
            ]
        );
    }
}
