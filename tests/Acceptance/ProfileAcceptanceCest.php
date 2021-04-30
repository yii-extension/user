<?php

declare(strict_types=1);

namespace Yii\Extension\User\Tests\Acceptance;

use Yii\Extension\User\Tests\AcceptanceTester;

final class ProfileAcceptanceCest
{
    public function testProfilePage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page.');
        $I->amOnPage('/profile');

        $I->expectTo('no see profile page.');
        $I->dontSee('Profile');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testProfilePageSuccess(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the profile page.');
        $I->amOnPage('/profile');

        $I->amGoingTo('see log in page.');
        $I->see('Log in');
        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello World');
        $I->see('My first website with Yii 3.0!');

        $I->amGoingTo('go to the login page.');
        $I->amOnPage('/profile');

        $I->expectTo('see profile page.');
        $I->seeInTitle('Profile');
    }

    /**
     * @depends Yii\Extension\User\Tests\Acceptance\RegisterAcceptanceCest:testRegisterSuccessDataDefaultAccountConfirmationFalse
     */
    public function testProfilePageUpdateData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the profile page.');
        $I->amOnPage('/profile');

        $I->amGoingTo('see log in page.');
        $I->see('Log in');
        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');

        $I->click('Log in', '#form-auth-login');

        $I->expectTo('see logged index page.');
        $I->see('Hello World');
        $I->see('My first website with Yii 3.0!');

        $I->amGoingTo('go to the login page.');
        $I->amOnPage('/profile');

        $I->expectTo('see profile page.');
        $I->seeInTitle('Profile');

        $I->fillField('#formprofile-name', 'Joe Doe');
        $I->fillField('#formprofile-publicemail', 'joedoe@example.com');
        $I->fillField('#formprofile-website', 'http://example.com');
        $I->fillField('#formprofile-location', 'Rusia');
        $I->selectOption('#formprofile-timezone', 'Europe/Moscow (UTC +03:00)');
        $I->fillField('#formprofile-bio', 'Developer Yii3.');

        $I->click('Save', '#form-profile-profile');

        $I->expectTo('see save data.');
        $I->seeInField('FormProfile[name]', 'Joe Doe');
        $I->seeInField('FormProfile[publicEmail]', 'joedoe@example.com');
        $I->seeInField('FormProfile[website]', 'http://example.com');
        $I->seeInField('FormProfile[location]', 'Rusia');
        $I->seeInField('FormProfile[timezone]', 'Europe/Moscow (UTC +03:00)');
        $I->seeInField('FormProfile[bio]', 'Developer Yii3.');
    }
}
