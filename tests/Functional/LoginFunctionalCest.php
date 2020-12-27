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
}
