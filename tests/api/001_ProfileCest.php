<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Codeception\Util\HttpCode;

/**
 * @group all
 * @group api
 * @group api_profile
 */
class ProfileCest
{
    use _support\ApiFixturesTrait;

    private $attachmentJsonType = [
        'username'   => 'string',
        'email'      => 'string',
        'roles'      => 'array',
        'first_name' => 'string|null',
        'last_name'  => 'string|null',
        'id'         => 'integer',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    public function get_current_user_profile_test(ApiTester $I)
    {
        $I->amGoingTo('Get profile for current user');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/profile');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->attachmentJsonType);

        $I->seeResponseContainsJson([
            'email' => $I->getUser(),
        ]);
    }
}
