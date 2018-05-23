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
 * @group structures
 */
class StructuresCest
{
    private $structure = 'selenium';
    private $structureType = 'seleniumType';

    public function create_new_structure_type_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new structure type');
        $I->amOnPage('/manageStructures');

        $I->fillField('input[name="create_structure_type_form[name]"]', $this->structureType);

        $I->click('[name="create_structure_type_form[submit]"]');
    }

    public function create_new_structure_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create a new structure');
        $I->amOnPage('/manageStructures');

        $I->fillField('input[name="create_structure_form[name]"]', $this->structure);

        $I->selectOptionFromSUISelect('create_structure_form[type]', $this->structureType);

        $I->click('[name="create_structure_form[submit]"]');
    }

    public function edit_newly_created_structure_type_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created structure type');
        $I->amOnPage('/manageStructures');

        $formName = 'form[name="edit_structure_type_form"]';

        $I->click('//td[contains(text(), "' . $this->structureType . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/editStructureType/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_structure_type_form[name]"]');
        $I->fillField('edit_structure_type_form[name]', 'edited-' . $this->structureType);
        $I->click($formName . ' [type="submit"]');

        // $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->structureType . '")]', 1);

        $I->click('//td[contains(text(), "edited-' . $this->structureType . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/editStructureType/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_structure_type_form[name]"]');
        $I->fillField('edit_structure_type_form[name]', $this->structureType);
        $I->click($formName . ' [type="submit"]');

        // $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->structureType . '")]', 1);
    }

    public function edit_newly_created_structure_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created structure');
        $I->amOnPage('/manageStructures');

        $formName = 'form[name="edit_structure_form"]';

        $I->click('//td[contains(text(), "' . $this->structure . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/editStructure/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_structure_form[name]"]');
        $I->fillField('edit_structure_form[name]', 'edited-' . $this->structure);
        $I->click($formName . ' [type="submit"]');

        // $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->structure . '")]', 1);

        $I->click('//td[contains(text(), "edited-' . $this->structure . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/editStructure/")]');
        $I->waitForElementVisible($formName . ' input[name="edit_structure_form[name]"]');
        $I->fillField('edit_structure_form[name]', $this->structure);
        $I->click($formName . ' [type="submit"]');

        // $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->structure . '")]', 1);
    }

    public function remove_newly_created_structure_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created structure');
        $I->amOnPage('/manageStructures');

        $I->click('//td[contains(text(), "' . $this->structure . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/removeStructure/")]');

        $formName = 'form[name="remove_structure_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $this->structure . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }

    public function remove_newly_created_structure_type_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created structure type');
        $I->amOnPage('/manageStructures');

        $I->click('//td[contains(text(), "' . $this->structure . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/removeStructureType/")]');

        $formName = 'form[name="remove_structure_type_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $this->structureType . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
