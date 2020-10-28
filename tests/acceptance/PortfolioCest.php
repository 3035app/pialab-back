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
 * @group portfolio
 */
class PortfolioCest
{
    private $portfolioName = 'selenium-portfolio';

    private $structure = 'selenium';
    private $structure2 = null;

    public function init_variables(Webguy $I)
    {
        $this->structure = $this->structure . rand(100, 999);
        $this->structure2 = $this->structure . '-2';
    }

    public function create_new_structure(Webguy $I)
    {
        $I->login();

        $structurePage = new \Page\StructurePage($I);
        $structurePage->createStructure($this->structure);
        $structurePage->createStructure($this->structure2);
    }

    /**
     * @depends create_new_structure
     */
    public function create_new_portfolio_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new portfolio');
        $I->amOnPage('/managePortfolios');

        $I->fillField('input[name="create_portfolio_form[name]"]', $this->portfolioName);

        $I->selectOptionFromSUISelect('create_portfolio_form[structures][]', $this->structure, false);
        $I->selectOptionFromSUISelect('create_portfolio_form[structures][]', $this->structure2);

        $I->click('[name="create_portfolio_form[submit]"]');

        $I->seeElement('//td[contains(text(), "' . $this->portfolioName . '")]');
        $I->logout();
    }

    /**
     * @depends create_new_portfolio_test
     */
    public function show_newly_created_portfolio_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Show newly created portfolio');
        $I->amOnPage('/managePortfolios');

        $I->click('//tr/td[2][contains(text(), "' . $this->portfolioName . '")]/ancestor::tr/descendant::a[contains(@href,"/showPortfolio/")]');

        $I->seeElement('//tr/td[2][contains(text(), "' . $this->structure . '")]');
        $I->seeElement('//tr/td[2][contains(text(), "' . $this->structure2 . '")]');
    }

    /**
     * @depends create_new_portfolio_test
     */
    public function edit_newly_created_portfolio_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created portfolio');
        $I->amOnPage('/managePortfolios');

        $formName = 'form[name="edit_portfolio_form"]';

        $I->click('//tr/td[2][contains(text(), "' . $this->portfolioName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePortfolios/editPortfolio/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_portfolio_form[name]"]');
        $I->fillField('edit_portfolio_form[name]', 'edited-' . $this->portfolioName);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->portfolioName . '")]', 1);

        $I->click('//tr/td[2][contains(text(), "edited-' . $this->portfolioName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePortfolios/editPortfolio/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_portfolio_form[name]"]');
        $I->fillField('edit_portfolio_form[name]', $this->portfolioName);
        $I->click($formName . ' [type="submit"]');
    }

    /**
     * @depends create_new_portfolio_test
     */
    public function unlink_structure_from_portfolio_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Unlink structure from newly created portfolio');
        $I->amOnPage('/managePortfolios');

        $I->click('//td[contains(text(), "' . $this->portfolioName . '")]/ancestor::tr/descendant::a[contains(@href,"/showPortfolio/")]');

        $I->seeElement('//td[contains(text(), "' . $this->structure2 . '")]');

        $I->click('//td[contains(text(), "' . $this->structure2 . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/dissociateStructure/")]');

        $I->cantSeeElement('//td[contains(text(), "' . $this->structure2 . '")]');
    }

    /**
     * @depends create_new_portfolio_test
     */
    public function remove_newly_created_portfolio_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created portfolio');
        $I->amOnPage('/managePortfolios');

        $I->click('//tr/td[2][contains(text(), "' . $this->portfolioName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePortfolios/removePortfolio/")]');

        $formName = 'form[name="remove_portfolio_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->click($formName . ' input[type="submit"]');
    }

    /**
     * @depends create_new_structure
     */
    public function remove_structure(Webguy $I)
    {
        $I->login();

        $structurePage = new \Page\StructurePage($I);
        $structurePage->removeStructure($this->structure);
        $structurePage->removeStructure($this->structure2);
    }
}
