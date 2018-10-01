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

class DataTypeDescriptor extends AbstractDescriptor
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $reference = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $data = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $retentionPeriod = '';

    /**
     * @JMS\Type("boolean")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $sensitive = false;

    public function __construct(
        string $reference,
        string $data = null,
        string $retentionPeriod = null,
        bool $sensitive = false
    ) {
        $this->reference = $reference;
        $this->data = $data;
        $this->retentionPeriod = $retentionPeriod;
        $this->sensitive = $sensitive;
    }
}
