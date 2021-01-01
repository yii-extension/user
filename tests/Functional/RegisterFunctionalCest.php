<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class RegisterFunctionalCest
{
    public function testRegisterSettingsRegisterFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings register false');
        $I->updateInDatabase('settings', ['register' => false], ['id' => 1]);

        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');

        $I->see('The page /register was not found.');

        $I->amGoingTo('update settings register true');
        $I->updateInDatabase('settings', ['register' => true], ['id' => 1]);
    }

    public function testRegisterSuccessDataDefaultAccountSettingsConfirmationTrue(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'administrator1@example.com',
            'Register[username]' => 'admin1',
            'Register[password]' => '123456'

        ]);

        $I->amGoingTo('go to the login page');
        $I->amOnPage('/login');

        $I->expectTo('see registration register validation.');
        $I->submitForm('#form-auth-login', [
            'Login[login]' => 'admin1',
            'Login[password]' => '123456',
        ]);
        $I->see('Please check your email to activate your account.');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }
}
