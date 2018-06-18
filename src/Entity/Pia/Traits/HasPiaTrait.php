<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia\Traits;

use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Pia;

trait HasPiaTrait
{
    /**
     * @JMS\Exclude()
     *
     * @var Pia
     */
    protected $pia;

    public function getPia(): Pia
    {
        return $this->pia;
    }

    public function setPia(Pia $pia): void
    {
        $this->pia = $pia;
    }

    /**
     * @JMS\VirtualProperty("pia_id")
     */
    public function getPiaId()
    {
        return $this->pia->getId();
    }
}
