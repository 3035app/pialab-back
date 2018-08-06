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
 * @group api_processing
 */
class ProcessingCest
{
    public const ROUTE = '/processings';

    private $processing = [];

    /**
     * @var array
     */
    private $processingData = [
        'name' => 'Processing CI',
        'folder_id' => null,
        'author' => 'Author 1',
        'controllers' => 'Controller 1, Controller 2, Controller 3'
    ];

    /**
     * @var array
     */
    private $processingJsonType = [
        'name'                  => 'string',
        'author'                => 'string',
        'status'                => 'integer',
        'description'           => 'string|null',
        'life_cycle'            => 'string|null',
        'storage'               => 'string|null',
        'standards'             => 'string|null',
        'processors'            => 'string|null',
        'controllers'           => 'string',
        'non_eu_transfer'       => 'string|null',
        'processing_data_types' => 'array',
        'pias'                  => 'array',
        'folder'                => 'array',
        'id'                    => 'integer',
        'created_at'            => 'string',
        'updated_at'            => 'string',
    ];

    public function create_processing_test(\ApiTester $I)
    {
        $I->amGoingTo('Create a new Processing');
        $I->login();

        $this->processingData['folder_id'] = $I->getRootFolderId();

        $I->sendJsonToCreate(ProcessingCest::ROUTE, $this->processingData);

        $I->seeCorrectJsonResponse($this->processingJsonType);
        $I->seeResponseContainsJson([
            'name' => $this->processingData['name'],
        ]);

        $this->processing = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_processing_test
     */
    public function list_processings_test(\ApiTester $I)
    {
        $I->amGoingTo('List available Processings');

        $I->login();

        $I->sendGET(ProcessingCest::ROUTE);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_processing_test
     */
    public function show_processing_test(\ApiTester $I)
    {
        $I->amGoingTo('Show newly created Processing, with id: ' . $this->processing['id']);
        $I->login();

        $I->sendJsonToShow(ProcessingCest::ROUTE . '/' . $this->processing['id']);

        $I->seeCorrectJsonResponse($this->processingJsonType);
        $I->seeResponseContainsJson([
            'name'  => $this->processingData['name'],
            'id'    => $this->processing['id'],
        ]);
    }

    /**
     * @depends create_processing_test
     */
    public function edit_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created processing, with id: ' . $this->processing['id']);
        $I->login();

        $name = $this->processing['name'] . '-edited';

        $data = array_merge($this->processing, [
            'name' => $name,
        ]);

        $I->sendJsonToEdit(ProcessingCest::ROUTE . '/' . $this->processing['id'], $data);

        $I->seeCorrectJsonResponse($this->processingJsonType);
        $I->seeResponseContainsJson([
            'name' => $name,
        ]);
    }


    /*
     * @depends create_processing_test
     */
    public function remove_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Remove processing, with id: ' . $this->processing['id']);
        $I->login();

        $I->sendJsonToDelete(ProcessingCest::ROUTE . '/' . $this->processing['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
