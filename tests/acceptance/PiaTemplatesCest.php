<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @group piaTemplates
 */
class PiaTemplatesCest
{
    private $piaTemplateName = 'seleniumTemplate';
    private $piaTemplateFilePath = __DIR__ . '/../_data/piaTemplate.json';

    public function create_new_pia_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Crate new PIA template');
        $I->amOnPage('/managePiaTemplates');

        $I->fillField('input[name="create_pia_template_form[name]"]', $this->piaTemplateName);
        $I->fillField('textarea[name="create_pia_template_form[description]"]', 'A selenium template');

        // Docker cannot access backend filesystem, so we need to find another way to upload a json example.
        $I->fillField('input[name="create_pia_template_form[data]"]', $this->piaTemplateFilePath);

        $I->click('form[name="create_pia_template_form"] [type="submit"]');
    }

    public function edit_newly_created_pia_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created PIA template');
        $I->amOnPage('/managePiaTemplates');

        $formName = 'form[name="edit_pia_template_form"]';

        $I->click('//td[contains(text(), "' . $this->piaTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePiaTemplates/editPiaTemplate/")]');
        $I->waitForElementVisible('input[name="edit_pia_template_form[name]"]');
        $I->fillField('edit_pia_template_form[name]', 'edited-' . $this->piaTemplateName);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->piaTemplateName . '")]', 1);

        $I->click('//td[contains(text(), "edited-' . $this->piaTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePiaTemplates/editPiaTemplate/")]');
        $I->waitForElementVisible('input[name="edit_pia_template_form[name]"]');
        $I->fillField('edit_pia_template_form[name]', $this->piaTemplateName);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->piaTemplateName . '")]', 1);
    }

    public function remove_newly_created_pia_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created PIA template');
        $I->amOnPage('/managePiaTemplates');

        $I->click('//td[contains(text(), "' . $this->piaTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/managePiaTemplates/removePiaTemplate/")]');

        $formName = 'form[name="remove_pia_template_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $this->piaTemplateName . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
