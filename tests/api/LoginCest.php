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
 * @group api_login
 */
class LoginCest
{
    public function login_test(ApiTester $I)
    {
        $I->amGoingTo('Login to the API');

        $I->login();

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseJsonMatchesJsonPath('access_token');
        $I->seeResponseJsonMatchesJsonPath('expires_in');
        $I->seeResponseJsonMatchesJsonPath('token_type');
        $I->seeResponseJsonMatchesJsonPath('scope');
        $I->seeResponseJsonMatchesJsonPath('refresh_token');
    }

    public function wrong_credentials_login_test(ApiTester $I)
    {
        $I->amGoingTo('Login to API with bad credentials');

        $I->setPassword('aWrongPassword');
        $I->login();
        $I->setPassword(null); // Reset password

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseJsonMatchesJsonPath('error');
        $I->seeResponseJsonMatchesJsonPath('error_description');

        $I->seeResponseContainsJson([
            'error'             => 'invalid_grant',
            'error_description' => 'Invalid username and password combination',
        ]);
    }

    /**
     * @depends login_test
     */
    public function refresh_token_test(ApiTester $I)
    {
        $I->amGoingTo('Refresh connection to API with a refresh token');

        $I->login();

        $I->sendGET($I->getBaseUrl() . '/oauth/v2/token', [
            'client_id'     => $I->getClientId(),
            'client_secret' => $I->getClientSecret(),
            'refresh_token' => $I->getRefreshToken(),
            'grant_type'    => 'refresh_token',
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseJsonMatchesJsonPath('access_token');
        $I->seeResponseJsonMatchesJsonPath('expires_in');
        $I->seeResponseJsonMatchesJsonPath('token_type');
        $I->seeResponseJsonMatchesJsonPath('scope');
        $I->seeResponseJsonMatchesJsonPath('refresh_token');
    }
}
