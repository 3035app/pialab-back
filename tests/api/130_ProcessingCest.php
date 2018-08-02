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
    use _support\ApiFixturesTrait;

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

    public function list_processings_test(\ApiTester $I)
    {
        $I->amGoingTo('List available Processings');

        $I->login();

        $I->sendGET('/processings');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}
