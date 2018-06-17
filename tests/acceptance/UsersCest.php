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
    private $firstname = 'John';
    private $lastname = 'Doe';
    private $email = 'selenium@pialab.io';
    private $password = 'kFR5C1EGaPZDFJ1A';

    public function create_new_super_admin_user_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new user');
        $I->amOnPage('/manageUsers');

        // Select Application
        $application = $I->grabTextFrom('//select[@name="create_user_form[application]"]/ancestor::div[contains(@class,"ui dropdown")]/div[contains(@class, "menu")]/div[contains(@class,"item")][1]');
        $I->selectOptionFromSUISelect('create_user_form[application]', $application);

        try {
            // Select Structure
            $structure = $I->grabTextFrom('//select[@name="create_user_form[structure]"]/ancestor::div[contains(@class,"ui dropdown")]/div[contains(@class, "menu")]/div[contains(@class,"item")][1]');
            $I->selectOptionFromSUISelect('create_user_form[structure]', $structure);
        } catch (\Exception $e) {
            // This part is optionnal
        }
        $I->fillField('input[name="create_user_form[profile][firstName]"]', $this->firstname);
        $I->fillField('input[name="create_user_form[profile][lastName]"]', $this->lastname);
        $I->fillField('input[name="create_user_form[email]"]', $this->email);
        $I->fillField('input[name="create_user_form[password]"]', $this->password);

        $I->checkSUIOption('input[name="create_user_form[roles][]"][value="ROLE_SUPER_ADMIN"]');

        $I->click('[name="create_user_form[submit]"]');
        $I->canSee($this->email, '//td');
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
        $I->click('[name="edit_user_form[submit]"]');

        // Changing from « edited-selenium@pialab.io » to « selenium@pialab.io »

        $I->click('//td[contains(text(), "edited-' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/editUser/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_user_form[email]"]');
        $I->fillField('edit_user_form[email]', $this->email);
        $I->click('[name="edit_user_form[submit]"]');
    }

    public function remove_newly_created_user_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created user');
        $I->amOnPage('/manageUsers');

        $I->click('//td[contains(text(), "' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/removeUser/")]');

        $formName = 'form[name="remove_user_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "E-mail")]/ancestor::tr/descendant::td[contains(text(), "' . $this->email . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
