<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange;

class DataExchangeDescriptor
{
    public static $structure = [
        'pia'         => [
            'status',
            'is_example',
            'numberOfQuestions',
            'name',
            'author_name',
            'evaluator_name',
            'validator_name',
            'dpo_status',
            'dpo_opinion',
            'concerned_people_opinion',
            'concerned_people_status',
            'concerned_people_searched_opinion',
            'concerned_people_searched_content',
            'rejection_reason',
            'applied_adjustements',
            'dpos_names',
            'people_names',
            'id',
            'created_at',
            'updated_at',
        ],
        'answers'     => [
        ],
        'measures'    => [
        ],
        'evaluations' => [
        ],
        'comments'    => [
        ],
        'attachments' => [
        ],
    ];
}
