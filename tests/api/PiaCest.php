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
    private $piaDatas = [
        'author_name'                       => 'codecept-author',
        'evaluator_name'                    => 'codecept-evaluator',
        'name'                              => 'codecept-name',
        'validator_name'                    => 'codecept-validator',
        'type'                              => 'regular',
        'concerned_people_searched_opinion' => 0,
    ];

    private $piaType = [
        'progress'                          => 'integer',
        'status'                            => 'integer',
        'name'                              => 'string',
        'author_name'                       => 'string',
        'evaluator_name'                    => 'string',
        'validator_name'                    => 'string',
        'dpo_status'                        => 'integer',
        'dpo_opinion'                       => 'string|null',
        'concerned_people_opinion'          => 'boolean|string|null',
        'concerned_people_status'           => 'integer',
        'concerned_people_searched_opinion' => 'boolean',
        'concerned_people_searched_content' => 'string|null',
        'rejection_reason'                  => 'string|null',
        'applied_adjustements'              => 'string|null',
        'dpos_names'                        => 'string|null',
        'people_names'                      => 'string|null',
        'is_example'                        => 'boolean',
        'folder'                            => 'array|null',
        'id'                                => 'integer',
        'created_at'                        => 'string',
        'updated_at'                        => 'string',
        'type'                              => 'string',
    ];

    private $pia = [];

    public function list_pias_test(ApiTester $I)
    {
        $I->amGoingTo('List available PIAs');

        $I->login();

        $I->amBearerAuthenticated($I->getToken());
        $I->sendGET($I->getBaseUrl() . '/pias');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function create_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create a PIA');

        $I->login();

        $I->amBearerAuthenticated($I->getToken());
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($I->getBaseUrl() . '/pias', $this->piaDatas);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->piaType);

        $this->pia = (array) $I->getPreviousResponse();
    }

    /**
     * @depends create_pia_test
     */
    public function edit_created_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit previous created PIA, with id: ' . $this->pia['id']);

        $I->login();

        $this->pia['name'] = 'codecept-name-edited';

        $I->amBearerAuthenticated($I->getToken());
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT($I->getBaseUrl() . '/pias/' . $this->pia['id'],
            array_merge($this->piaDatas, json_decode(json_encode($this->pia), JSON_OBJECT_AS_ARRAY))
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->piaType);

        $I->canSeeResponseContainsJson(['name' => 'codecept-name-edited']);
    }

    /**
     * @depends create_pia_test
     */
    public function remove_created_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previous created PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->amBearerAuthenticated($I->getToken());
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE($I->getBaseUrl() . '/pias/' . $this->pia['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
