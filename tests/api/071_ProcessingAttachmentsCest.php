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
 * @group api_processing_attachments
 */
class ProcessingAttachmentsCest
{
    use _support\ApiFixturesTrait;

    private $attachmentJsonType = [
        'name'          => 'string',
        'mime_type'     => 'string',
        'file'          => 'string',
        'id'            => 'integer',
        'created_at'    => 'string',
        'updated_at'    => 'string',
    ];

    private $attachmentData = [
        'name'      => 'codecept-attachment',
        'mime_type' => 'image/png',
        'file'      => 'data:application/octet-stream;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QYQChMZxoasPAAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAuHSURBVHja5Zt5cFXVHcc/5963Z3lZISGBpEAC2SAExHFrZRlFHRxrVSRop3Xs2EGnVhDQVkBRURB1/MPRjss4oBSm4xTGThWUBFBLZClrNggJewgBsvCyvOXe0z+SkJC8LS8vCPTMZN7Nu7/zO+f3Pb/t/M55gnC0OQuT0bkDwQwQE0CmAGbgEnAc2IdkO4KjKPphPl/VzDXSxIA5FC58HfhLv/pIuQ0hF7F21U/XNwCFC/cD4wbAoQHYAvo81q46ef0AUDjPAIZypBzduaR9WYp+s94ILGbtyoPXAQALX0R…ZA+vG22PmPnKtJzdVxUAgCVFZdkg9/kDwaAILrS5Lmw9ef7i/rMNiSgiJoRflF1Z8pLUzBiVZC4Yak+0GgyPLP5V1oZQ2Q14XV7ZfDBeMyg/AmP8ppxCoAjhKj5ZX3r4oiO7rrXd4fbo0Qg6Ujvh3U+CBEmrxai2JUVYjBOT46omJEYXuDVdSiF+s2xK9j8HMv+wKOaS4kMCKT4Dfhu89iKllPrxS22V+841nzvb0mZoc2tmj66bVEVxR5mUtrRom8yNt6cMi7SOQnQXzZA064qe+dqUvLqBzj2slrmkuKxA6qwVQo7pzwSEEJ2fvTW9IyPsFRRXeKS+dPm0PGc45jwotz+XFJc+iOQNIDNMLF3Adg3xh9enZh8L51wH9frrC98fiDO5DZ+AfCBEFh4h5VIpxBvLpuYMyk3Vq3L/9+WicqOU+lhdMEZAPoh8hByKJAaJDUED0CThqIADSA5KweFXp+bUDPbc/gcNaTkevF6KBgAAAABJRU5ErkJggg==',
    ];

    private $attachment = [];

    public function create_processing_test(ApiTester $I)
    {
        $this->createTestProcessing($I);
    }

    /**
     * @depends create_processing_test
     */
    public function list_processing_attachments_for_processing_test(ApiTester $I)
    {
        $I->amGoingTo('List attachments for specific Processing');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/processings/' . $this->processing['id'] . '/attachments');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_processing_test
     */
    public function create_an_attachment_for_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Create an attachment for a specific Processing');

        $I->login();

        $attachmentData = array_replace_recursive($this->attachmentData, [
            'processing_id' => $this->processing['id'],
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/processings/' . $this->processing['id'] . '/attachments', $attachmentData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType($this->attachmentJsonType);

        $this->attachment = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_an_attachment_for_processing_test
     */
    public function show_created_processing_attachment_for_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Show previously created processing attachment, with id: ' . $this->attachment['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/processings/' . $this->processing['id'] . '/attachments/' . $this->attachment['id'], $this->attachment);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->attachmentJsonType);

        $I->canSeeResponseContainsJson([
            'name' => $this->attachmentData['name'],
        ]);
    }

    /**
     * @depends create_an_attachment_for_processing_test
     */
    public function remove_created_processing_attachment_for_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previously created processing attachment, with id: ' . $this->processing['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/processings/' . $this->processing['id'] . '/attachments/' . $this->attachment['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @depends create_processing_test
     */
    public function remove_processing_test(ApiTester $I)
    {
        $this->removeTestProcessing($I);
        $this->removeTestProcessing($I);
    }
}
