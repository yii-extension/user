<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Yii\Extension\User\Tests\FunctionalTester;

final class AccountFunctionalCest
{
    public function testEmailChangeStrategyEmptyValues(FunctionalTester $I): void
    {
        $I->amGoingTo('email change strategy empty');
        $I->amOnPage('/attempt/email');

        $I->expectTo('error 404 response');
        $I->seeResponseCodeIs(404);
    }

    public function testEmailChangeStrategyWrongToken(FunctionalTester $I): void
    {
        $I->amGoingTo('email change strategy empty');
        $I->amOnPage('/attempt/email/1/NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6');

        $I->expectTo('error 404 response');
        $I->seeResponseCodeIs(404);
    }

    public function testEmailChangeStrategyInsecure(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings email change strategy insecure');
        $I->updateInDatabase('settings', ['emailChangeStrategy' => 0], ['id' => 1]);

        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the account page');
        $I->amOnPage('/account');
        $I->see('Account setting');

        $I->amGoingTo('change email register user');
        $I->submitForm('#form-setting-account', [
            'Account[email]' => 'administrator100@example.com',
        ]);

        $I->expectTo('see email change strategy insecure');
        $I->seeInField('Account[email]', 'administrator100@example.com');

        $I->amGoingTo('update settings email change strategy default');
        $I->updateInDatabase('settings', ['emailChangeStrategy' => 1], ['id' => 1]);
    }

    public function testEmailChangeStrategyDefault(FunctionalTester $I): void
    {
        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the account page');
        $I->amOnPage('/account');
        $I->see('Account setting');

        $I->amGoingTo('change email register user');
        $I->submitForm('#form-setting-account', [
            'Account[email]' => 'administrator@example.com',
        ]);

        $I->expectTo('see email change strategy default');
        $I->seeInField('Account[email]', 'administrator@example.com');

        $I->expectTo('see email change strategy default');
        $emailUnconfirmed = $I->grabColumnFromDatabase('user', 'unconfirmed_email', ['id' => 1]);
        $token = $I->grabColumnFromDatabase('token', 'code', ['user_id' => 1, 'type' => 2]);

        $I->expectTo('find the unconfirmed email registered in the database.');
        $I->assertSame('administrator@example.com', $emailUnconfirmed[0]);

        $I->expectTo('confirm user by token');
        $I->amOnPage('/attempt/email/1/' . $token[0]);

        $I->expectTo('go to account page');
        $I->see('Account setting');
        $I->seeInField('Account[email]', 'administrator@example.com');

        $emailUnconfirmed = $I->grabColumnFromDatabase('user', 'unconfirmed_email', ['id' => 1]);

        $I->expectTo('unconfirmed mail null in database.');
        $I->assertNull($emailUnconfirmed[0]);
    }

    public function testEmailChangeStrategySecure(FunctionalTester $I): void
    {
        $I->amGoingTo('update settings email change strategy secure');
        $I->updateInDatabase('settings', ['emailChangeStrategy' => 2], ['id' => 1]);

        $I->amGoingTo('go to the log in page');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello!');
        $I->see("Let's start something great with Yii3!");
        $I->see('Logout (admin)');

        $I->amGoingTo('go to the account page');
        $I->amOnPage('/account');
        $I->see('Account setting');

        $I->amGoingTo('change email register user');
        $I->submitForm('#form-setting-account', [
            'Account[email]' => 'administrator100@example.com',
        ]);

        $I->expectTo('see email change strategy secure');
        $I->seeInField('Account[email]', 'administrator100@example.com');

        $I->expectTo('see email change strategy secure first confirmation email.');
        $emailUnconfirmed = $I->grabColumnFromDatabase('user', 'unconfirmed_email', ['id' => 1]);
        $token = $I->grabColumnFromDatabase('token', 'code', ['user_id' => 1, 'type' => 2]);

        $I->expectTo('find the unconfirmed email registered in the database.');
        $I->assertSame('administrator100@example.com', $emailUnconfirmed[0]);

        $I->expectTo('confirm user by token mail administrator100@example.com');
        $I->amOnPage('/attempt/email/1/' . $token[0]);

        $I->expectTo('go to account page');
        $I->see('Account setting');
        $I->seeInField('Account[email]', 'administrator100@example.com');
        $I->see('Please check your email to confirm the change');

        $I->expectTo('see email change strategy secure second confirmation email.');
        $token = $I->grabColumnFromDatabase('token', 'code', ['user_id' => 1, 'type' => 3]);

        $I->expectTo('confirm user by token mail administrator@example.com');
        $I->amOnPage('/attempt/email/1/' . $token[0]);

        $I->expectTo('go to account page');
        $I->see('Account setting');
        $I->seeInField('Account[email]', 'administrator100@example.com');

        $emailUnconfirmed = $I->grabColumnFromDatabase('user', 'unconfirmed_email', ['id' => 1]);

        $I->expectTo('unconfirmed mail null in database.');
        $I->assertNull($emailUnconfirmed[0]);

        $I->amGoingTo('update settings email change strategy default');
        $I->updateInDatabase('settings', ['emailChangeStrategy' => 1], ['id' => 1]);
    }
}
