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
class ProcessingDataTypeCest
{
    use _support\ApiFixturesTrait;

    public const ROUTE = '/processing-data-types';

    private $processingDataType = [];

    /**
     * @var array
     */
    private $processingDataTypeData = [
        'reference'         => 'Reference CI',
        'processing_id'     => null,
        'retention_period'  => 'period',
        'sensitive'         => true,
        'data'              => ['data' => 'edited'],
    ];

    /**
     * @var array
     */
    private $processingDataTypeJsonType = [
        'reference'         => 'string|null',
        'data'              => 'array',
        'retention_period'  => 'string|null',
        'sensitive'         => 'boolean',
    ];

    public function create_processing_data_type_test(\ApiTester $I)
    {
        $this->createTestProcessing($I);

        $I->amGoingTo('Create a new ProcessingDataType');
        $I->login();

        $this->processingDataTypeData['processing_id'] = $this->processing['id'];

        $I->sendJsonToCreate(ProcessingDataTypeCest::ROUTE, $this->processingDataTypeData);

        $I->seeCorrectJsonResponse($this->processingDataTypeJsonType);

        $this->processingDataType = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_processing_data_type_test
     */
    public function list_processing_data_types_test(\ApiTester $I)
    {
        $I->amGoingTo('List available ProcessingDataTypes');

        $I->login();

        $I->sendGET(ProcessingDataTypeCest::ROUTE);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_processing_data_type_test
     */
    public function show_processing_data_type_test(\ApiTester $I)
    {
        $I->amGoingTo('Show newly created ProcessingDataType, with id: ' . $this->processingDataType['id']);
        $I->login();

        $I->sendJsonToShow(ProcessingDataTypeCest::ROUTE . '/' . $this->processingDataType['id']);

        $I->seeCorrectJsonResponse($this->processingDataTypeJsonType);
        $I->seeResponseContainsJson([
            'id'    => $this->processingDataType['id'],
        ]);
    }

    /**
     * @depends create_processing_data_type_test
     */
    public function edit_processing_data_type_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created ProcessingDataType, with id: ' . $this->processingDataType['id']);
        $I->login();

        $reference = 'Reference edited';
        $data_field = ['data' => 'edited'];

        $data = array_merge($this->processingDataType, [
            'reference' => $reference,
            'data'      => $data_field,
        ]);

        $I->sendJsonToEdit(ProcessingDataTypeCest::ROUTE . '/' . $this->processingDataType['id'], $data);

        $I->seeCorrectJsonResponse($this->processingDataTypeJsonType);
        $I->seeResponseContainsJson([
            'reference' => $reference,
        ]);
    }

    /*
     * @depends create_processing_data_type_test
     */
    public function remove_processing_data_type_test(ApiTester $I)
    {
        $I->amGoingTo('Remove ProcessingDataType, with id: ' . $this->processingDataType['id']);
        $I->login();

        $I->sendJsonToDelete(ProcessingDataTypeCest::ROUTE . '/' . $this->processingDataType['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
