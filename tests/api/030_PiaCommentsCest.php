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
 * @group api_pia_comments
 */
class PiaCommentsCest
{
    use _support\ApiFixturesTrait;

    private $commentJsonType = [
        'pia_id'       => 'integer',
        'description'  => 'string|null',
        'reference_to' => 'string|null',
        'for_measure'  => 'boolean',
        'id'           => 'integer',
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];

    private $commentData = [
        'pia_id'       => null,
        'description'  => 'codecept-comment',
        'for_measure'  => false,
        'reference_to' => '111',
    ];

    private $comment = [];

    public function create_pia_test(ApiTester $I)
    {
        $this->createTestPia($I);
    }

    /**
     * @depends create_pia_test
     */
    public function list_pia_comments_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List comments for specific PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/comments');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_pia_test
     */
    public function create_pia_comment_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create an comment for specific PIA');

        $I->login();

        $commentData = array_replace_recursive($this->commentData, [
            'pia_id' => $this->pia['id'],
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/comments', $commentData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->commentJsonType);

        $this->comment = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_pia_comment_for_pia_test
     */
    public function show_created_pia_comment_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previous created PIA comment, with id: ' . $this->comment['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/comments/' . $this->comment['id'], $this->comment);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->commentJsonType);

        $I->canSeeResponseContainsJson(['description' => $this->commentData['description']]);
    }

    /**
     * @depends create_pia_comment_for_pia_test
     */
    public function edit_created_pia_comment_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit previous created PIA comment, with id: ' . $this->comment['id']);

        $I->login();

        $this->comment['description'] = $this->commentData['description'] . '-edited';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/comments/' . $this->comment['id'], $this->comment);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->commentJsonType);

        $I->canSeeResponseContainsJson(['description' => $this->commentData['description'] . '-edited']);
    }

    /**
     * @depends create_pia_comment_for_pia_test
     */
    public function remove_created_pia_comment_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previous created comment PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/comments/' . $this->comment['id']);

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
