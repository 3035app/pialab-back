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
 * @group api_pia_answers
 */
class PiaAnswersCest
{
    use _support\ApiFixturesTrait;

    private $answerJsonType = [
        'pia_id'       => 'integer',
        'reference_to' => 'string|null',
        'data'         => [
            'text'  => 'string|null',
            'gauge' => 'integer|null',
            'list'  => 'array|null',
        ],
        'id'         => 'integer',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    private $answerData = [
        'pia_id'       => null,
        'reference_to' => '111', // First answer
        'data'         => [
            'text'  => 'codecept-answer',
            'gauge' => null,
            'list'  => [],
        ],
    ];

    private $answer = [];

    public function create_pia_test(ApiTester $I)
    {
        $this->createTestProcessing($I);
        $this->createTestPia($I);
    }

    /**
     * @depends create_pia_test
     */
    public function list_pia_answers_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List answers for specific PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/pias/' . $this->pia['id'] . '/answers');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_pia_test
     */
    public function create_pia_answer_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create an answer for specific PIA');

        $I->login();

        $answerData = array_replace_recursive($this->answerData, [
            'pia_id' => $this->pia['id'],
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/pias/' . $this->pia['id'] . '/answers', $answerData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->answerJsonType);

        $this->answer = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_pia_answer_for_pia_test
     */
    public function show_created_pia_answer_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previously created PIA answer, with id: ' . $this->answer['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/pias/' . $this->pia['id'] . '/answers/' . $this->answer['id'], $this->answer);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->answerJsonType);

        $I->canSeeResponseContainsJson(['data' => ['text' => $this->answerData['data']['text']]]);
    }

    /**
     * @depends create_pia_answer_for_pia_test
     */
    public function edit_created_pia_answer_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit previously created PIA answer, with id: ' . $this->answer['id']);

        $I->login();

        $this->answer['data']['text'] = $this->answerData['data']['text'] . '-edited';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/pias/' . $this->pia['id'] . '/answers/' . $this->answer['id'], $this->answer);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->answerJsonType);

        $I->canSeeResponseContainsJson(['data' => ['text' => $this->answerData['data']['text'] . '-edited']]);
    }

    /**
     * @depends create_pia_answer_for_pia_test
     */
    public function remove_created_pia_answer_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previously created answer PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/pias/' . $this->pia['id'] . '/answers/' . $this->answer['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @depends create_pia_test
     */
    public function remove_pia_test(ApiTester $I)
    {
        $this->removeTestPia($I);
        $this->removeTestProcessing($I);
    }
}
