<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @group all
 * @group applications
 */
class ApplicationsCest
{
    private $application = 'selenium';
    private $applicationUrl = 'http://selenium.test';

    public function create_new_application_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Crate new application');
        $I->amOnPage('/manageApplications');

        $I->fillField('input[name="create_application_form[name]"]', $this->application);
        $I->fillField('input[name="create_application_form[url]"]', $this->applicationUrl);

        $I->click('.application-form-add-uri');

        $I->fillField('input[name="create_application_form[redirectUris][0]"]', $this->applicationUrl);

        $I->click('form[name="create_application_form"] [type="submit"]');
    }

    public function edit_newly_created_application_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created application');
        $I->amOnPage('/manageApplications');

        $formName = 'form[name="edit_application_form"]';

        $I->click('//td[contains(text(), "' . $this->application . '")]/ancestor::tr/descendant::a[contains(@href,"/manageApplications/editApplication/")]');
        $I->waitForElementVisible('input[name="edit_application_form[name]"]');
        $I->fillField('edit_application_form[name]', 'edited-' . $this->application);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->application . '")]', 1);

        $I->click('//td[contains(text(), "edited-' . $this->application . '")]/ancestor::tr/descendant::a[contains(@href,"/manageApplications/editApplication/")]');
        $I->waitForElementVisible('input[name="edit_application_form[name]"]');
        $I->fillField('edit_application_form[name]', $this->application);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->application . '")]', 1);
    }

    public function remove_newly_created_application_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created application');
        $I->amOnPage('/manageApplications');

        $I->click('//td[contains(text(), "' . $this->application . '")]/ancestor::tr/descendant::a[contains(@href,"/manageApplications/removeApplication/")]');

        $formName = 'form[name="remove_application_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $this->application . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
