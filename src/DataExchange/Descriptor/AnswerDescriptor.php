<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Descriptor;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class AnswerDescriptor extends AbstractDescriptor
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $referenceTo = '';

    /**
     * @JMS\Type("array")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var array
     */
    protected $data = [];

    /**
     * @JMS\Type("DateTime")
     * @JMS\Groups({"Export"})
     *
     * @var \DateTime|null
     */
    protected $createdAt = '';

    /**
     * @JMS\Type("DateTime")
     * @JMS\Groups({"Export"})
     *
     * @var \DateTime|null
     */
    protected $updatedAt = '';

    public function __construct(
        string $referenceTo,
        array $data,
        \DateTime $createdAt,
        \DateTime $updatedAt
    ) {
        $this->referenceTo = $referenceTo;
        $this->data = $data;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
