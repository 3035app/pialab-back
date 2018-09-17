<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia\Traits;

trait StructureInformationsTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $siren;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $siret;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $vatNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $activityCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $legalForm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var \DateTime
     */
    protected $registrationDate;

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(?string $address = null): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone = null): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getSiren(): ?string
    {
        return $this->siren;
    }

    /**
     * @param string $siren
     */
    public function setSiren(?string $siren = null): void
    {
        $this->siren = $siren;
    }

    /**
     * @return string
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     */
    public function setSiret(?string $siret = null): void
    {
        $this->siret = $siret;
    }

    /**
     * @return string
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     */
    public function setVatNumber(?string $vatNumber = null): void
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getActivityCode(): ?string
    {
        return $this->activityCode;
    }

    /**
     * @param string $activityCode
     */
    public function setActivityCode(?string $activityCode = null): void
    {
        $this->activityCode = $activityCode;
    }

    /**
     * @return string
     */
    public function getLegalForm(): ?string
    {
        return $this->legalForm;
    }

    /**
     * @param string $legalForm
     */
    public function setLegalForm(?string $legalForm = null): void
    {
        $this->legalForm = $legalForm;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTime $registrationDate
     */
    public function setRegistrationDate(?\DateTime $registrationDate = null): void
    {
        $this->registrationDate = $registrationDate;
    }
}
