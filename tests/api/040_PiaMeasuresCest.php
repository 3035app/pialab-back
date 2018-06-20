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
 * @group api_pia_measures
 */
class PiaMeasuresCest
{
    use _support\ApiFixturesTrait;

    private $measureJsonType = [
        'pia_id'      => 'integer',
        'title'       => 'string',
        'content'     => 'string',
        'placeholder' => 'string',
        'id'          => 'integer',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];

    private $measureData = [
        'pia_id'      => null,
        'content'     => '',
        'title'       => '',
        'placeholder' => 'measures.default_placeholder',
    ];

    private $measure = [];

    public function create_pia_test(ApiTester $I)
    {
        $this->createTestPia($I);
    }

    /**
     * @depends create_pia_test
     */
    public function list_pia_measures_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List measures for specific PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_pia_test
     */
    public function create_an_empty_pia_measure_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create a measure for specific PIA');

        $I->login();

        $measureData = array_replace_recursive($this->measureData, [
            'pia_id' => $this->pia['id'],
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures', $measureData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->measureJsonType);

        $this->measure = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_an_empty_pia_measure_for_pia_test
     */
    public function edit_title_of_created_pia_measure_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit title of previous created PIA measure, with id: ' . $this->measure['id']);

        $I->login();

        $this->measure['title'] = 'codecept-measure';
        $this->measureData['title'] = $this->measure['title'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures/' . $this->measure['id'], $this->measure);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->measureJsonType);

        $I->canSeeResponseContainsJson(['title' => $this->measureData['title']]);
    }

    /**
     * @depends create_an_empty_pia_measure_for_pia_test
     */
    public function edit_content_of_created_pia_measure_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit content of previous created PIA measure, with id: ' . $this->measure['id']);

        $I->login();

        $this->measure['content'] = 'codecept-measure-description';
        $this->measureData['content'] = $this->measure['content'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures/' . $this->measure['id'], $this->measure);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->measureJsonType);

        $I->canSeeResponseContainsJson(['content' => $this->measureData['content']]);
    }

    /**
     * @depends create_an_empty_pia_measure_for_pia_test
     */
    public function show_created_pia_measure_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previous created PIA measure, with id: ' . $this->measure['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures/' . $this->measure['id'], $this->measure);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->measureJsonType);

        $I->canSeeResponseContainsJson([
            'content'     => $this->measureData['content'],
            'title'       => $this->measureData['title'],
        ]);
    }

    /**
     * @depends create_an_empty_pia_measure_for_pia_test
     */
    public function remove_created_pia_measure_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previous created measure PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/measures/' . $this->measure['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @depends create_pia_test
     */
    public function remove_pia_test(ApiTester $I)
    {
        $this->removeTestPia($I);
    }
}
