<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_template")
 */
class PiaTemplate implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="json")
     *
     * @var string
     */
    protected $data;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $importedFileName;

    /**
     * @ORM\OneToMany(targetEntity="Pia", mappedBy="template")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $pias;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->pias = new ArrayCollection();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getPias(): Collection
    {
        return $this->pias;
    }

    /**
     * @param Collection $pias
     */
    public function setPias(Collection $pias): void
    {
        $this->pias = $pias;
    }

    /**
     * @return string
     */
    public function getImportedFileName(): string
    {
        return $this->importedFileName;
    }

    /**
     * @param string $importedFileName
     */
    public function setImportedFileName(string $importedFileName): void
    {
        $this->importedFileName = $importedFileName;
    }

    /**
     * Add file to the pia_template.
     *
     * @param UploadedFile $file
     */
    public function addFile(UploadedFile $file): void
    {
        $content = file_get_contents($file->getPathname());
        $this->setData($content);
        $this->setImportedFileName($file->getClientOriginalName());
    }
}
