<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @group processingTemplates
 */
class ProcessingTemplatesCest
{
    private $processingTemplateName = 'seleniumTemplate';
    private $processingTemplateFilePath = __DIR__ . '/../_data/processingTemplate.json';

    public function create_new_processing_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Create new Processing template');
        $I->amOnPage('/manageProcessingTemplates');

        $I->fillField('input[name="create_processing_template_form[name]"]', $this->processingTemplateName);
        $I->fillField('textarea[name="create_processing_template_form[description]"]', 'A selenium template');

        // Docker cannot access backend filesystem, so we need to find another way to upload a json example.
        $I->fillField('input[name="create_processing_template_form[data]"]', $this->processingTemplateFilePath);

        $I->click('form[name="create_processing_template_form"] [type="submit"]');
    }

    public function edit_newly_created_processing_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Edit newly created Processing template');
        $I->amOnPage('/manageProcessingTemplates');

        $formName = 'form[name="edit_processing_template_form"]';

        $I->click('//td[contains(text(), "' . $this->processingTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/manageProcessingTemplates/editProcessingTemplate/")]');
        $I->waitForElementVisible('input[name="edit_processing_template_form[name]"]');
        $I->fillField('edit_processing_template_form[name]', 'edited-' . $this->processingTemplateName);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "edited-' . $this->processingTemplateName . '")]', 1);

        $I->click('//td[contains(text(), "edited-' . $this->processingTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/manageProcessingTemplates/editProcessingTemplate/")]');
        $I->waitForElementVisible('input[name="edit_processing_template_form[name]"]');
        $I->fillField('edit_processing_template_form[name]', $this->processingTemplateName);
        $I->click($formName . ' [type="submit"]');

        $I->canSeeNumberOfElements('//td[contains(text(), "' . $this->processingTemplateName . '")]', 1);
    }

    public function remove_newly_created_processing_template_test(Webguy $I)
    {
        $I->login();

        $I->wantTo('Remove newly created Processing template');
        $I->amOnPage('/manageProcessingTemplates');

        $I->click('//td[contains(text(), "' . $this->processingTemplateName . '")]/ancestor::tr/descendant::a[contains(@href,"/manageProcessingTemplates/removeProcessingTemplate/")]');

        $formName = 'form[name="remove_processing_template_form"]';

        $I->waitForElementVisible($formName . ' input[type="submit"]');

        $I->canSeeNumberOfElements('//table[@class="ui single line table"]/descendant-or-self::b[contains(text(), "Nom")]/ancestor::tr/descendant::td[contains(text(), "' . $this->processingTemplateName . '")]', 1);

        $I->click($formName . ' input[type="submit"]');
    }
}
