<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @group all
 * @group users
 */
class UsersCest
{
    private $email = 'selenium@pialab.io';
    private $password = 'kFR5C1EGaPZDFJ1A';

    public function create_new_super_admin_user_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new user');
        $I->amOnPage('/manageUsers');

        $formName = 'form[name="create_user_form"]';

        // Select Application
        $application = $I->grabTextFrom('//select[@name="create_user_form[application]"]/ancestor::div[contains(@class,"ui dropdown")]/div[contains(@class, "menu")]/div[contains(@class,"item")][1]');
        $I->selectOptionFromSUISelect('create_user_form[application]', $application);

        // Select Structure
        $structure = $I->grabTextFrom('//select[@name="create_user_form[structure]"]/ancestor::div[contains(@class,"ui dropdown")]/div[contains(@class, "menu")]/div[contains(@class,"item")][1]');
        $I->selectOptionFromSUISelect('create_user_form[structure]', $structure);

        $I->fillField('input[name="create_user_form[email]"]', $this->email);
        $I->fillField('input[name="create_user_form[password]"]', $this->password);

        $I->checkSUIOption('input[name="create_user_form[roles][]"][value="ROLE_SUPER_ADMIN"]');

        $I->click($formName . ' input[type="submit"]');
    }

    public function login_with_newly_created_user(Webguy $I)
    {
        $I->wantTo('Log-in with newly created user');
        $I->login($this->email, $this->password);
        $I->logout();
    }

    public function edit_newly_created_user_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created user');
        $I->amOnPage('/manageUsers');

        $formName = 'form[name="edit_user_form"]';

        // Changing from « selenium@pialab.io » to « edited-selenium@pialab.io »

        $I->click('//td[contains(text(), "' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/editUser/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_user_form[email]"]');
        $I->fillField('edit_user_form[email]', 'edited-' . $this->email);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->email . '")]', 1);

        // Changing from « edited-selenium@pialab.io » to « selenium@pialab.io »

        $I->click('//td[contains(text(), "edited-' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/editUser/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_user_form[email]"]');
        $I->fillField('edit_user_form[email]', $this->email);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->email . '")]', 1);
    }

    public function remove_newly_created_user_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created user');
        $I->amOnPage('/manageUsers');

        $I->click('//td[contains(text(), "' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/removeUser/")]');

        $formName = 'form[name="remove_user_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Email")]/ancestor::tr/descendant::td[contains(text(), "' . $this->email . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
