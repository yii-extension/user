<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class RequestFunctionalCest
{
    public function testRequestSettingsPasswordRecoveryFalse(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings password recovery false.');
        $I->updateInDatabase('settings', ['passwordRecovery' => false], ['id' => 1]);

        $I->amGoingTo('go to the request page.');
        $I->amOnPage('/request');

        $I->expectTo('no see request page.');
        $I->see('We were unable to find the page /request.');

        $I->amGoingTo('update settings password recovery true.');
        $I->updateInDatabase('settings', ['passwordRecovery' => true], ['id' => 1]);
    }

    public function testRequestAccountConfirmationTrue(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings confirmation true.');
        $I->updateInDatabase('settings', ['confirmation' => true], ['id' => 1]);

        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/register');

        $I->expectTo('see registration register form.');
        $I->submitForm('#form-registration-register', [
            'Register[email]' => 'request@example.com',
            'Register[username]' => 'request',
            'Register[password]' => '123456',
        ]);

        $I->amGoingTo('go to the request page.');
        $I->amOnPage('/request');

        $I->expectTo('see request form.');
        $I->submitForm('#form-recovery-request', [
            'Request[email]' => 'request@example.com',
        ]);

        $I->expectTo('see error request form.');
        $I->see('Inactive user');

        $I->amGoingTo('update settings confirmation false');
        $I->updateInDatabase('settings', ['confirmation' => false], ['id' => 1]);
    }
}
