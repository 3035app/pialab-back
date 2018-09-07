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
            'status'                            => null,
            'is_example'                        => null,
            'numberOfQuestions'                 => null,
            'author_name'                       => null,
            'evaluator_name'                    => null,
            'validator_name'                    => null,
            'dpo_status'                        => null,
            'dpo_opinion'                       => null,
            'concerned_people_opinion'          => null,
            'concerned_people_status'           => null,
            'concerned_people_searched_opinion' => null,
            'concerned_people_searched_content' => null,
            'rejection_reason'                  => null,
            'applied_adjustements'              => null,
            'dpos_names'                        => null,
            'people_names'                      => null,
            'created_at'                        => null,
            'updated_at'                        => null,
        ],
        'answers'     => [
            'pia_id'       => null,
            'reference_to' => null,
            'data'         => [
                'text'  => null,
                'gauge' => null,
                'list'  => null,
            ],
            'created_at' => null,
            'updated_at' => null,
        ],
        'measures'    => [
            'pia_id'      => null,
            'title'       => null,
            'content'     => null,
            'placeholder' => null,
            'created_at'  => null,
            'updated_at'  => null,
        ],
        'evaluations' => [
            'status'  => null,
            'gauges'  => [
                'x' => null,
                'y' => null,
            ],
            'global_status'                 => null,
            'pia_id'                        => null,
            'reference_to'                  => null,
            'action_plan_comment'           => null,
            'evaluation_comment'            => null,
            'evaluation_date'               => null,
            'estimated_implementation_date' => null,
            'person_in_charge'              => null,
            'created_at'                    => null,
            'updated_at'                    => null,
        ],
        'comments'    => [
            'pia_id'       => null,
            'description'  => null,
            'reference_to' => null,
            'for_measure'  => null,
            'created_at'   => null,
            'updated_at'   => null,
        ],
        'attachments' => [
            'pia_signed' => null,
            'pia_id'     => null,
            'name'       => null,
            'mime_type'  => null,
            'file'       => null,
            'created_at' => null,
            'updated_at' => null,
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
