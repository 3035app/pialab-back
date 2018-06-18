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
use Doctrine\Common\Collections\Collection;

trait HasManyPiasTrait
{
    /**
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $pias;

    /**
     * @return array|Pia[]
     */
    public function getPias(): array
    {
        return $this->pias->getValues();
    }

    /**
     * @param Pia $pia
     *
     * @throws \InvalidArgumentException
     */
    public function addPia(Pia $pia): void
    {
        if ($this->pias->contains($pia)) {
            throw new \InvalidArgumentException(sprintf('Pia « %s » is already in %s', $pia, self::class));
        }
        $this->pias->add($pia);
    }

    /**
     * @param Pia $pia
     *
     * @throws \InvalidArgumentException
     */
    public function removePia(Pia $pia): void
    {
        if (!$this->pias->contains($pia)) {
            throw new \InvalidArgumentException(sprintf('Pia « %s » is not in %s', $pia, self::class));
        }
        $this->pias->removeElement($pia);
    }
}
