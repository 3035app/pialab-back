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
 */
class BackendCest
{
    public function loginAndLogoutTest(Webguy $I)
    {
        $I->login();
        $I->logout();
    }
}
