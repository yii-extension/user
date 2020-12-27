<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class RegisterFunctionalCest
{
    public function testRequestSettingsRegisterFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings register false');
        $I->updateInDatabase('settings', ['register' => false], ['id' => 1]);

        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');

        $I->see('The page /register not found.');

        $I->amGoingTo('update settings register true');
        $I->updateInDatabase('settings', ['register' => true], ['id' => 1]);
    }

    public function testRegisterSuccessDataDefaultAccountSettingsConfirmationTrue(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the register page');
        $I->amOnPage('/register');

        $I->fillField('#register-email', 'administrator1@example.com');
        $I->fillField('#register-username', 'admin1');
        $I->fillField('#register-password', '123456');

        $I->click('Register', '#form-registration-register');

        $I->expectTo('see registration register validation.');
        $I->see('Please check your email to activate your username.');
        $I->dontSeeLink('Register', '#form-registration-register');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }
}
