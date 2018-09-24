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
    private $route = '';

    /**
     * @var array
     */
    private $processingData = [
        'name'                  => 'Processing CI',
        'folder'                => [],
        'author'                => 'Author 1',
        'designated_controller' => 'Designated controller',
    ];

    private $importDataPia = [
        'folder_id'  => 1,
        'processing' => [
            'name'            => 'ProcessingCI edited',
            'author'          => 'Author edited',
            'controllers'     => 'Controllers edited',
            'description'     => 'Description edited',
            'processors'      => 'Processors edited',
            'non_eu_transfer' => 'non eu transfer edited',
            'life_cycle'      => 'life cycle edited',
            'storage'         => 'storage edited',
            'standards'       => 'standards edited',
            'status'          => 'STATUS_ARCHIVED',
            'created_at'      => '2018-08-01T17:17:16+0200',
            'updated_at'      => '2018-08-03T11:55:27+0200',
            'pias'            => [[
                'name'                              => 'codecept-name-edited',
                'status'                            => 'SIGNED_VALIDATION',
                'author_name'                       => 'codecept-author',
                'evaluator_name'                    => 'codecept-evaluator',
                'validator_name'                    => 'codecept-validator',
                'dpo_status'                        => 0,
                'dpo_opinion'                       => '',
                'concerned_people_opinion'          => '',
                'concerned_people_status'           => 0,
                'concerned_people_searched_opinion' => false,
                'concerned_people_searched_content' => '',
                'rejection_reason'                  => '',
                'applied_adjustments'               => '',
                'dpos_names'                        => 'dpos',
                'people_names'                      => '',
                'is_example'                        => false,
                'type'                              => 'regular',
            ]],
        ],
    ];

    /**
     * @var array
     */
    private $processingJsonType = [
        'name'                      => 'string',
        'author'                    => 'string',
        'status'                    => 'integer',
        'description'               => 'string|null',
        'life_cycle'                => 'string|null',
        'storage'                   => 'string|null',
        'standards'                 => 'string|null',
        'processors'                => 'string|null',
        'designated_controller'     => 'string',
        'non_eu_transfer'           => 'string|null',
        'context_of_implementation' => 'string|null',
        'recipients'                => 'string|null',
        'lawfulness'                => 'string|null',
        'minimization'              => 'string|null',
        'rights_guarantee'          => 'string|null',
        'exactness'                 => 'string|null',
        'consent'                   => 'string|null',
        'processing_data_types'     => 'array',
        'pias_count'                => 'integer',
        'folder'                    => 'array',
        'id'                        => 'integer',
        'created_at'                => 'string',
        'updated_at'                => 'string',
    ];

    public function import_processing_with_pia_test(\ApiTester $I)
    {
        $I->amGoingTo('Import a processing with a pia');
        $I->login();

        $this->importDataPia['folder_id'] = $I->getRootFolder()['id'];

        $I->sendJsonToCreate(ProcessingCest::ROUTE . '/import', $this->importDataPia);

        $I->seeResponseContainsJson([
            'name'  => $this->importDataPia['processing']['name'],
        ]);
    }

    public function create_processing_test(\ApiTester $I)
    {
        $I->amGoingTo('Create a new Processing');
        $I->login();

        $this->processingData['folder'] = $I->getRootFolder();

        $I->sendJsonToCreate(ProcessingCest::ROUTE, $this->processingData);

        $I->seeCorrectJsonResponse($this->processingJsonType);
        $I->seeResponseContainsJson([
            'name' => $this->processingData['name'],
        ]);

        $this->processing = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);

        $this->route = ProcessingCest::ROUTE . '/' . $this->processing['id'];
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

        $I->sendJsonToShow($this->route);

        $I->seeCorrectJsonResponse($this->processingJsonType);
        $I->seeResponseContainsJson([
            'name'  => $this->processingData['name'],
            'id'    => $this->processing['id'],
        ]);
    }

    /*
     * @depends create_processing_test
     */
    public function export_processing_test(ApiTester $I)
    {
        $I->amGoingTo('Export a processing with id: ' . $this->processing['id']);
        $I->login();

        $I->setJsonHeader();
        $I->sendGET($this->route . '/export');

        $I->seeResponseContainsJson([
            'name'  => $this->processingData['name'],
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

        $I->sendJsonToEdit($this->route, $data);

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

        $I->sendJsonToDelete($this->route);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
