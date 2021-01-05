<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class ResendFormCest
{
    public function testResendAccountConfirmationTrue(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->expectTo('see resend page.');
        $I->seeInTitle('Resend confirmation message');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }

    public function testResendAccountConfirmationTrueEmptyDataTest(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->click('Continue', '#form-recovery-resend');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank.');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }

    public function testResendAccountConfirmationTrueSubmitFormWrongData(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->fillField('#resend-email', 'noExist');

        $I->click('Continue', '#form-recovery-resend');

        $I->expectTo('see validations errors.');
        $I->see('This value is not a valid email address.');

        $I->fillField('#resend-email', 'noExist@example.com');

        $I->click('Continue', '#form-recovery-resend');

        $I->expectTo('see validations errors.');
        $I->see('Thank you. If said email is registered, you will get a password reset.');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }

    public function testResendAccountConfirmationTrueUserIsActive(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->fillField('#resend-email', 'administrator@example.com');

        $I->click('Continue', '#form-recovery-resend');

        $I->expectTo('see validations errors.');
        $I->see('User is active.');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }

    /**
     * @depends Yii\Extension\User\Tests\Functional\RegisterFunctionalCest:testRegisterSuccessDataDefaultAccountSettingsConfirmationTrue
     */
    public function registrationResendEmailOptionsDefaultAccountConfirmationTrue(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the resend page');
        $I->amOnPage('/resend');

        $I->fillField('#resend-email', 'administrator1@example.com');

        $I->click('Continue', '#form-recovery-resend');

        $I->expectTo('go to page index');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->dontSeeLink('Continue', '#form-recovery-register');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }
}
