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
 * @group api_admin_structures
 */
class AdminStructuresCest
{
    /**
     * @var array
     */
    private $structureData = [
        'name' => 'StructureCI',
    ];

    /**
     * @var array
     */
    private $structureJsonType = [
        'id'                => 'integer',
        'name'              => 'string',
        'type'              => 'array|null',
        'rootFolder'        => 'array',
        'portfolio'         => 'null|array',
        'created_at'        => 'string',
        'updated_at'        => 'string',
        'address'           => 'string|null',
        'phone'             => 'string|null',
        'siren'             => 'string|null',
        'siret'             => 'string|null',
        'vat_number'        => 'string|null',
        'activity_code'     => 'string|null',
        'legal_form'        => 'string|null',
        'registration_date' => 'string|null',
    ];

    private $structure = [];

    public function create_structure_test(ApiTester $I)
    {
        $I->amGoingTo('Create a new Structure');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/structures', $this->structureData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->structureJsonType);
        $I->seeResponseContainsJson([
            'name' => $this->structureData['name'],
        ]);

        $this->structure = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_structure_test
     */
    public function show_structure_test(ApiTester $I)
    {
        $I->amGoingTo('Show newly created Structure, with id: ' . $this->structure['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/structures/' . $this->structure['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->structureJsonType);
        $I->seeResponseContainsJson([
            'name'  => $this->structureData['name'],
            'id'    => $this->structure['id'],
        ]);
    }

    /**
     * @depends create_structure_test
     */
    public function list_structures_test(ApiTester $I)
    {
        $I->amGoingTo('Show all Structures');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/structures');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_structure_test
     */
    public function edit_structure_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created Structure, with id: ' . $this->structure['id']);

        $I->login();

        $name = $this->structure['name'] . '-edited';

        $data = array_merge($this->structure, [
            'name' => $name,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/structures/' . $this->structure['id'], $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->structureJsonType);
        $I->seeResponseContainsJson([
            'name' => $name,
        ]);
    }

    /*
     * @depends create_structure_test
     */
    public function remove_structure_test(ApiTester $I)
    {
        $I->amGoingTo('Remove Structure, with id: ' . $this->structure['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/structures/' . $this->structure['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function getStructure(): ?array
    {
        return $this->structure;
    }
}
