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
 * @group api_pia_evaluations
 */
class PiaEvaluationsCest
{
    use _support\ApiFixturesTrait;

    private $evaluationJsonType = [
        'pia_id'                        => 'integer',
        'status'                        => 'integer',
        'reference_to'                  => 'string',
        'action_plan_comment'           => 'string|null',
        'evaluation_comment'            => 'string|null',
        'evaluation_date'               => 'string',
        'gauges'                        => [
            'x' => 'integer',
            'y' => 'integer',
        ],
        'estimated_implementation_date' => 'string',
        'person_in_charge'              => 'string|null',
        'global_status'                 => 'integer',
        'id'                            => 'integer',
        'created_at'                    => 'string',
        'updated_at'                    => 'string',
    ];

    private $evaluationData = [
        'pia_id'                        => null,
        'action_plan_comment'           => null,
        'estimated_implementation_date' => '2018-06-12T09:23:57+02:00',
        'evaluation_comment'            => null,
        'gauges'                        => [
            'x' => 0,
            'y' => 0,
        ],
        'global_status'    => 0,
        'person_in_charge' => null,
        'reference_to'     => '1.1',
        'status'           => 3,
    ];

    private $evaluation = [];

    public function create_pia_test(ApiTester $I)
    {
        $this->createTestProcessing($I);
        $this->createTestPia($I);
    }

    /**
     * @depends create_pia_test
     */
    public function list_pia_evaluations_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List evaluations for specific PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/pias/' . $this->pia['id'] . '/evaluations');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_pia_test
     */
    public function create_an_evaluation_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create an evaluation for specific PIA');

        $I->login();

        $evaluationData = array_replace_recursive($this->evaluationData, [
            'pia_id'           => $this->pia['id'],
            'evaluation_date'  => (new \DateTime('NOW'))->format(\DateTime::ISO8601),
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/pias/' . $this->pia['id'] . '/evaluations', $evaluationData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->evaluationJsonType);

        $this->evaluation = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_an_evaluation_for_pia_test
     */
    public function edit_created_pia_evaluation_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit content of previously created PIA evaluation, with id: ' . $this->evaluation['id']);

        $I->login();

        $this->evaluation['evaluation_comment'] = 'codecept-evaluation-comment';
        $this->evaluationData['evaluation_comment'] = $this->evaluation['evaluation_comment'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/pias/' . $this->pia['id'] . '/evaluations/' . $this->evaluation['id'], $this->evaluation);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->evaluationJsonType);

        $I->canSeeResponseContainsJson(['evaluation_comment' => $this->evaluationData['evaluation_comment']]);
    }

    /**
     * @depends create_an_evaluation_for_pia_test
     */
    public function show_created_pia_evaluation_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previously created PIA evaluation, with id: ' . $this->evaluation['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/pias/' . $this->pia['id'] . '/evaluations/' . $this->evaluation['id'], $this->evaluation);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->evaluationJsonType);

        $I->canSeeResponseContainsJson([
            'evaluation_comment' => $this->evaluationData['evaluation_comment'],
        ]);
    }

    /**
     * @depends create_an_evaluation_for_pia_test
     */
    public function remove_created_pia_evaluation_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previously created evaluation PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/pias/' . $this->pia['id'] . '/evaluations/' . $this->evaluation['id']);

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
