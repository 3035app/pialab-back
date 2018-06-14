<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Page;

class StructurePage
{
    /**
     * @var AcceptanceTester
     */
    protected $tester;

    public function __construct(\WebGuy $I)
    {
        $this->tester = $I;
    }

    public function createStructureType($structureType)
    {
        $I = $this->tester;
        $I->wantTo(sprintf('Create a new structure type named "%s"', $structureType));
        $I->amOnPage('/manageStructures');
        $I->fillField('input[name="create_structure_type_form[name]"]', $structureType);
        $I->click('[name="create_structure_type_form[submit]"]');
        $I->canSee($structureType, '//td');

        return $this;
    }

    public function createStructure($structure, $structureType)
    {
        $I = $this->tester;
        $I->wantTo(sprintf('Create a new structure named "%s" of type "%s"', $structure, $structureType));
        $I->amOnPage('/manageStructures');
        $I->fillField('input[name="create_structure_form[name]"]', $structure);
        $I->selectOptionFromSUISelect('create_structure_form[type]', $structureType);
        $I->click('[name="create_structure_form[submit]"]');

        $I->canSee($structure, '//td');

        return $this;
    }

    public function removeStructure($structure)
    {
        $I = $this->tester;
        $I->wantTo(sprintf('Remove the structure named "%s"', $structure));
        $I->amOnPage('/manageStructures');
        $I->click('//td[contains(text(), "' . $structure . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/removeStructure/")]');

        $formName = 'form[name="remove_structure_form"]';
        $I->waitForElementVisible($formName . ' input[type="submit"]');
        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $structure . '")]', 1);

        $I->click($formName . ' input[type="submit"]');

        $I->dontSee($structure, '//td');
    }

    public function removeStructureType($structureType)
    {
        $I = $this->tester;
        $I->wantTo(sprintf('Remove the structure type named "%s"', $structureType));
        $I->amOnPage('/manageStructures');
        $I->click('//td[contains(text(), "' . $structureType . '")]/ancestor::tr/descendant::a[contains(@href,"/manageStructures/removeStructureType/")]');

        $formName = 'form[name="remove_structure_type_form"]';
        $I->waitForElementVisible($formName . ' input[type="submit"]');
        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $structureType . '")]', 1);

        $I->click($formName . ' input[type="submit"]');

        $I->dontSee($structureType, '//td');
    }
}
