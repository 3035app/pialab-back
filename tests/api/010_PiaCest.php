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
 * @group api_pia
 */
class PiaCest
{
    use _support\ApiFixturesTrait;

    public function list_pias_test(\ApiTester $I)
    {
        $I->amGoingTo('List available PIAs');

        $I->login();

        $I->sendGET('/pias');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function create_pia_test(\ApiTester $I)
    {
        $I->amGoingTo('Create a PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/pias', $this->piaDatas);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->piaJsonType);

        $this->pia = (array) $I->getPreviousResponse();
    }

    /**
     * @depends create_pia_test
     */
    public function show_pia_test(\ApiTester $I)
    {
        $I->amGoingTo('Show a PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/pias', $this->piaDatas);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->piaJsonType);

        $I->seeResponseContainsJson(['name' => $this->piaDatas['name']]);
    }

    /**
     * @depends create_pia_test
     */
    public function edit_created_pia_test(\ApiTester $I)
    {
        $I->amGoingTo('Edit previously created PIA, with id: ' . $this->pia['id']);

        $I->login();

        $this->pia['name'] = $this->piaDatas['name'] . '-edited';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/pias/' . $this->pia['id'],
            array_merge($this->piaDatas, json_decode(json_encode($this->pia), JSON_OBJECT_AS_ARRAY))
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->piaJsonType);

        $I->canSeeResponseContainsJson(['name' => $this->piaDatas['name'] . '-edited']);
    }

    /**
     * @depends create_pia_test
     */
    public function remove_created_pia_test(\ApiTester $I)
    {
        $I->amGoingTo('Remove previously created PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/pias/' . $this->pia['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
