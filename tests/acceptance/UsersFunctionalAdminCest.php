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
class UsersFunctionalAdminCest
{
    private $firstname = 'John';
    private $lastname = 'Doe';
    private $email = 'selenium@pialab.io';
    private $password = 'kFR5C1EGaPZDFJ1A';

    private $dpoFirstname = 'DpoJohn';
    private $dpoLastname = 'DpoDoe';
    private $dpoEmail = 'dpo-selenium@pialab.io';
    private $dpoPassword = 'DpokFR5C1EGaPZDFJ1A';

    private $structure = 'AdminStructure SAS';
    private $structureType = 'AdminStructureType';

    public function init_variables(Webguy $I)
    {
        $this->structure = $this->structure . rand(100, 999);
        $this->structureType = $this->structureType . rand(100, 999);
    }

    public function create_new_structure(Webguy $I)
    {
        $I->login();

        $structurePage = new \Page\StructurePage($I);
        $structurePage->createStructureType($this->structureType);
        $structurePage->createStructure($this->structure, $this->structureType);
    }

    public function create_new_functional_admin(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new functional admin');
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

        $I->checkSUIOption('input[name="create_user_form[roles][]"][value="ROLE_ADMIN"]');

        $I->click('[name="create_user_form[submit]"]');

        $I->seeElement('//td[contains(text(), "' . $this->email . '")]');
        $I->logout();
    }

    public function login_with_newly_created_functional_admin(Webguy $I)
    {
        $I->wantTo('Log-in with newly created function admin');
        $I->login($this->email, $this->password);
        $I->amOnPage('/manageUsers');

        //Function Admin should not see other menus
        $I->expect('All super_admin menus are not visible');
        $I->dontSeeNavMenuWithHref('/manageStructures');
        $I->dontSeeNavMenuWithHref('/managePiaTemplates');
        $I->dontSeeNavMenuWithHref('/manageApplications');

        $I->logout();
    }

    public function create_new_dpo_with_functional_admin(Webguy $I)
    {
        $I->login($this->email, $this->password);

        $I->wantTo('Create a new dpo');
        $I->amOnPage('/manageUsers');

        // No Application Choice
        $I->expect('Application and Structure are not selectable');
        $I->dontSeeElement('//select[@name="create_user_form[application]"]');
        $I->dontSeeElement('//select[@name="create_user_form[structure]"]');

        $I->fillField('input[name="create_user_form[profile][firstName]"]', $this->dpoFirstname);
        $I->fillField('input[name="create_user_form[profile][lastName]"]', $this->dpoLastname);
        $I->fillField('input[name="create_user_form[email]"]', $this->dpoEmail);
        $I->fillField('input[name="create_user_form[password]"]', $this->dpoPassword);

        $I->checkSUIOption('input[name="create_user_form[roles][]"][value="ROLE_DPO"]');

        $I->click('[name="create_user_form[submit]"]');

        $I->seeElement('//td[contains(text(), "' . $this->dpoEmail . '")]');
        $I->logout();
    }

    public function remove_newly_created_dpo_with_functional_admin(Webguy $I)
    {
        $I->login($this->email, $this->password);

        $I->wantTo('Remove newly created dpo');
        $I->amOnPage('/manageUsers');

        $I->click('//td[contains(text(), "' . $this->dpoEmail . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/removeUser/")]');

        $formName = 'form[name="remove_user_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "E-mail")]/ancestor::tr/descendant::td[contains(text(), "' . $this->email . '")]', 1);

        $I->click($formName . ' input[type="submit"]');

        $I->expect('DPO is removed from the list');
        $I->dontSeeElement('//td[contains(text(), "' . $this->dpoEmail . '")]');

        $I->logout();
    }

    public function remove_newly_created_funtional_admin(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created function admin');
        $I->amOnPage('/manageUsers');

        $I->click('//td[contains(text(), "' . $this->email . '")]/ancestor::tr/descendant::a[contains(@href,"/manageUsers/removeUser/")]');

        $formName = 'form[name="remove_user_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "E-mail")]/ancestor::tr/descendant::td[contains(text(), "' . $this->email . '")]', 1);

        $I->click($formName . ' input[type="submit"]');

        $I->expect('Functional Admin is removed from the list');
        $I->dontSeeElement('//td[contains(text(), "' . $this->email . '")]');

        $I->logout();
    }

    public function remove_structure(Webguy $I)
    {
        $I->login();

        $structurePage = new \Page\StructurePage($I);
        $structurePage->removeStructure($this->structure);
        $structurePage->removeStructureType($this->structureType);
    }
}
