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
 * @group api_pia_folder
 */
class PiaFoldersCest
{
    use _support\ApiFixturesTrait;

    private $folderJsonType = [
    ];

    private $folderData = [
        'pia_id'                        => null,
        'action_plan_comment'           => null,
        'estimated_implementation_date' => '2018-06-12T09:23:57+02:00',
        'folder_comment'                => null,
        'gauges'                        => [
            'x' => 0,
            'y' => 0,
        ],
        'global_status'    => 0,
        'person_in_charge' => null,
        'reference_to'     => '1.1',
        'status'           => 3,
    ];

    private $folder = [];

    public function create_pia_test(ApiTester $I)
    {
        $this->createTestPia($I);
    }

    /**
     * @depends create_pia_test
     */
    public function list_pia_folders_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List folders for specific PIA');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/folders');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_pia_test
     */
    public function create_an_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Create an folder for specific PIA');

        $I->login();

        $folderData = array_replace_recursive($this->folderData, [
            'pia_id' => $this->pia['id'],
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/folders', $folderData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $this->folder = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_an_folder_for_pia_test
     */
    public function edit_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit content of previous created PIA folder, with id: ' . $this->folder['id']);

        $I->login();

        $this->folder['folder_comment'] = 'codecept-folder-comment';
        $this->folderData['folder_comment'] = $this->folder['folder_comment'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/folders/' . $this->folder['id'], $this->folder);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $I->canSeeResponseContainsJson(['folder_comment' => $this->folderData['folder_comment']]);
    }

    /**
     * @depends create_an_folder_for_pia_test
     */
    public function show_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previous created PIA folder, with id: ' . $this->folder['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/folders/' . $this->folder['id'], $this->folder);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $I->canSeeResponseContainsJson([
            'folder_comment' => $this->folderData['folder_comment'],
        ]);
    }

    /**
     * @depends create_an_folder_for_pia_test
     */
    public function remove_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previous created folder PIA, with id: ' . $this->pia['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE($I->getBaseUrl() . '/pias/' . $this->pia['id'] . '/folders/' . $this->folder['id']);

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
