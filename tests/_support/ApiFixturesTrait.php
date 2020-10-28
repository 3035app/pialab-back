<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace _support;

trait ApiFixturesTrait
{
    private $piaDatas = [
        'author_name'                       => 'codecept-author',
        'evaluator_name'                    => 'codecept-evaluator',
        'validator_name'                    => 'codecept-validator',
        'type'                              => 'regular',
        'concerned_people_searched_opinion' => 0,
        'processing'                        => [
            'id' => null,
        ],
    ];

    private $piaJsonType = [
        'progress'                          => 'integer',
        'status'                            => 'integer',
        'author_name'                       => 'string',
        'evaluator_name'                    => 'string',
        'validator_name'                    => 'string',
        'dpo_status'                        => 'integer',
        'dpo_opinion'                       => 'string|null',
        'concerned_people_opinion'          => 'boolean|string|null',
        'concerned_people_status'           => 'integer',
        'concerned_people_searched_opinion' => 'boolean',
        'concerned_people_searched_content' => 'string|null',
        'rejection_reason'                  => 'string|null',
        'applied_adjustments'               => 'string|null',
        'dpos_names'                        => 'string|null',
        'people_names'                      => 'string|null',
        'is_example'                        => 'boolean',
        'id'                                => 'integer',
        'created_at'                        => 'string',
        'updated_at'                        => 'string',
        'type'                              => 'string',
    ];

    private $pia = [];

    private function createTestPia(\ApiTester $I): void
    {
        $I->login();

        $I->sendJsonToCreate('/pias', $this->piaDatas);

        $this->pia = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    private function removeTestPia(\ApiTester $I): void
    {
        $I->login();

        $I->sendJsonToDelete('/pias/' . $this->pia['id']);

        $this->pia = [];
    }

    private function createTestProcessing(\ApiTester $I): void
    {
        $I->login();

        $processingData = [
            'name'                  => 'Processing CI',
            'folder'                => $I->getRootFolder(),
            'author'                => 'Author 1',
            'designated_controller' => 'Designated controller',
        ];

        $I->sendJsonToCreate('/processings', $processingData);

        $this->processing = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);

        $this->piaDatas['processing']['id'] = $this->processing['id'];
    }

    private function removeTestProcessing(\ApiTester $I): void
    {
        $I->login();

        $I->sendJsonToDelete('/processings/' . $this->processing['id']);

        $this->processing = null;
        $this->piaDatas['processing']['id'] = null;
    }
}
