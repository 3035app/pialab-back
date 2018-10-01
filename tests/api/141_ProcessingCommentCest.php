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
 * @group api_processing_comment
 */
class ProcessingCommentCest
{
    use _support\ApiFixturesTrait;

    public const ROUTE = '/processing-comments';

    private $processingComment = [];

    /**
     * @var array
     */
    private $processingCommentData = [
        'content'       => 'Comment content',
        'processing_id' => null,
        'field'         => 'description',
    ];

    /**
     * @var array
     */
    private $processingCommentJsonType = [
        'content' => 'string',
        'field'   => 'string',
    ];

    public function create_processing_comment_test(\ApiTester $I)
    {
        $this->createTestProcessing($I);

        $I->amGoingTo('Create a new ProcessingComment');
        $I->login();

        $this->processingCommentData['processing_id'] = $this->processing['id'];

        $I->sendJsonToCreate(ProcessingCommentCest::ROUTE, $this->processingCommentData);

        $I->seeCorrectJsonResponse($this->processingCommentJsonType);

        $this->processingComment = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_processing_comment_test
     */
    public function list_processing_comments_test(\ApiTester $I)
    {
        $I->amGoingTo('List available ProcessingComments');

        $I->login();

        $I->sendGET('/processings/' . $this->processing['id'] . '/comments');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_processing_comment_test
     */
    public function show_processing_comment_test(\ApiTester $I)
    {
        $I->amGoingTo('Show newly created ProcessingComment, with id: ' . $this->processingComment['id']);
        $I->login();

        $I->sendJsonToShow(ProcessingCommentCest::ROUTE . '/' . $this->processingComment['id']);

        $I->seeCorrectJsonResponse($this->processingCommentJsonType);
        $I->seeResponseContainsJson([
            'id' => $this->processingComment['id'],
        ]);
    }

    /**
     * @depends create_processing_comment_test
     */
    public function edit_processing_comment_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created ProcessingComment, with id: ' . $this->processingComment['id']);
        $I->login();

        $content = 'edited content';
        $field = 'edited field';

        $data = array_merge($this->processingComment, [
            'content' => $content,
            'field'      => $field,
        ]);

        $I->sendJsonToEdit(ProcessingCommentCest::ROUTE . '/' . $this->processingComment['id'], $data);

        $I->seeCorrectJsonResponse($this->processingCommentJsonType);
        $I->seeResponseContainsJson([
            'content' => $content,
            'field'   => $field,
        ]);
    }

    /**
     * @depends create_processing_comment_test
     */
    public function remove_processing_comment_test(ApiTester $I)
    {
        $I->amGoingTo('Remove ProcessingComment, with id: ' . $this->processingComment['id']);
        $I->login();

        $I->sendJsonToDelete(ProcessingCommentCest::ROUTE . '/' . $this->processingComment['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
