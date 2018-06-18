<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange;

use JMS\Serializer\Annotation as JMS;

class DataExchangeDescriptor
{
    const STRUCTURE = [
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
            'created_at',
            'updated_at',
        ],
        'answers'     => [
            'pia_id',
            'reference_to',
            'data'    => [
                'text',
                'gauge',
                'list',
            ],
            'created_at',
            'updated_at',
        ],
        'measures'    => [
            'pia_id',
            'title',
            'content',
            'placeholder',
            'created_at',
            'updated_at',
        ],
        'evaluations' => [
            'status',
            'gauges'  => [
                'x',
                'y',
            ],
            'global_status',
            'pia_id',
            'reference_to',
            'action_plan_comment',
            'evaluation_comment',
            'evaluation_date',
            'estimated_implementation_date',
            'person_in_charge',
            'created_at',
            'updated_at',
        ],
        'comments'    => [
            'pia_id',
            'description',
            'reference_to',
            'for_measure',
            'created_at',
            'updated_at',
        ],
        'attachments' => [
            'pia_signed',
            'pia_id',
            'name',
            'mime_type',
            'file',
            'created_at',
            'updated_at',
        ],
    ];

    /**
     * @JMS\Groups({"Export"})
     *
     * @var mixed
     */
    public $pia;

    /**
     * @JMS\Groups({"Export"})
     *
     * @var array
     */
    public $answers;

    /**
     * @JMS\Groups({"Export"})
     *
     * @var array
     */
    public $measures;

    /**
     * @JMS\Groups({"Export"})
     *
     * @var array
     */
    public $evaluations;

    /**
     * @JMS\Groups({"Export"})
     *
     * @var array
     */
    public $comments;

    /**
     * @JMS\Groups({"Export"})
     *
     * @var array
     */
    public $attachments;
}
