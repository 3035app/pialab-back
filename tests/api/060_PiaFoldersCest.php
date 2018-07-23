<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Codeception\Util\HttpCode;
use PiaApi\Entity\Pia\Folder;

/**
 * @group all
 * @group api
 * @group api_pia_folder
 */
class PiaFoldersCest
{
    use _support\ApiFixturesTrait;

    private $folderJsonType = [
        'isRoot'     => 'boolean',
        'path'       => 'string',
        'hierarchy'  => 'array',
        'name'       => 'string',
        'lft'        => 'integer',
        'lvl'        => 'integer',
        'rgt'        => 'integer',
        'parent'     => 'array|null',
        'children'   => 'array',
        'pias'       => 'array',
        'id'         => 'integer',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    private $folderData = [
        'name' => 'codecept-folder',
    ];

    private $folder = [];

    private $rootFolderId;

    public function create_root_folder_test(ApiTester $I)
    {
        $I->amGoingTo('Create root folder');

        $I->login();

        $rootData = [
            'name' => 'codecept-root',
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/folders', $rootData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $this->rootFolderId = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY)['id'];
    }

    /**
     * @depends create_root_folder_test
     */
    public function list_pia_folders_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('List folders');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/folders');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_root_folder_test
     */
    public function create_a_folder_test(ApiTester $I)
    {
        $I->amGoingTo('Create a folder for parent #' . $this->rootFolderId);

        $I->login();

        $this->folderData['parent'] = [
            'id' => $this->rootFolderId,
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/folders', $this->folderData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $this->folder = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_a_folder_test
     */
    public function edit_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Edit content of previously created folder, with id: ' . $this->folder['id']);

        $I->login();

        $this->folder['name'] = 'codecept-folder-edited';
        $this->folderData['name'] = $this->folder['name'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/folders/' . $this->folder['id'], $this->folder);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $I->canSeeResponseContainsJson(['name' => $this->folderData['name']]);
    }

    /**
     * @depends create_a_folder_test
     */
    public function move_folder_into_created_one_test(ApiTester $I)
    {
        $I->amGoingTo('Move a folder into folder, with id: ' . $this->folder['id']);

        $I->login();

        // Create folder to be moved

        $folderToBeMovedData = [
            'name'   => 'codecept-folder-to-be-moved',
            'parent' => [
                'id' => $this->rootFolderId,
            ],
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/folders', $folderToBeMovedData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);
        $I->canSeeResponseContainsJson(['parent' => ['id' => $this->rootFolderId]]);

        $folderToBeMoved = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);

        // Move folder to previously created folder

        $folderToBeMoved['parent'] = [
            'id' => $this->folder['id'],
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/folders/' . $folderToBeMoved['id'], $folderToBeMoved);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);
        $I->canSeeResponseContainsJson(['parent' => ['id' => $this->folder['id']]]);
    }

    /**
     * @depends create_a_folder_test
     */
    public function show_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Show previously created folder, with id: ' . $this->folder['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/folders/' . $this->folder['id'], $this->folder);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->folderJsonType);

        $I->canSeeResponseContainsJson([
            'name' => $this->folderData['name'],
        ]);
    }

    /**
     * @depends create_a_folder_test
     */
    public function remove_created_pia_folder_for_pia_test(ApiTester $I)
    {
        $I->amGoingTo('Remove previously created folder, with id: ' . $this->folder['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/folders/' . $this->folder['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_root_folder_test
     */
    public function remove_root_folder_test(ApiTester $I)
    {
        $I->amGoingTo('Remove root folder, with id: ' . $this->rootFolderId);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/folders/' . $this->rootFolderId);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}
