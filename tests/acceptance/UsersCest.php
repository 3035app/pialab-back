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
        $I->amOnPage('/manageUsers');

        $formName = 'form[name="create_user_form"]';

        // Select Application
        $application = $I->grabTextFrom($formName . ' select[name="create_user_form[application]"] option:nth-child(1)');
        dump($application);
        $I->selectOption($formName . ' select[name="create_user_form[application]"]', $application);

        // Select Structure
        $structure = $I->grabTextFrom($formName . ' select[name="create_user_form[structure]"] option:nth-child(1)');
        dump($structure);
        $I->selectOption($formName . ' select[name="create_user_form[structure]"]', $structure);

        $I->fillField($formName . ' input[name="create_user_form[email]"]', $this->email);
        $I->fillField($formName . ' input[name="create_user_form[password]"]', $this->password);

        $I->checkSUIOption($formName . ' input[name="create_user_form[roles][]"][value="ROLE_SUPER_ADMIN"]');

        $I->click($formName . ' [type="submit"]');
    }

    public function login_with_newly_created_user(Webguy $I)
    {
        $I->login($this->email, $this->password);
        $I->logout();
    }
}
