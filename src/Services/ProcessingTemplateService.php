<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Pia\ProcessingTemplate;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProcessingTemplateService extends AbstractService
{
    public function __construct(
        RegistryInterface $doctrine
    ) {
        parent::__construct($doctrine);
    }

    public function getEntityClass(): string
    {
        return ProcessingTemplate::class;
    }

    /**
     * @param string      $name
     * @param string      $jsonContent
     * @param string      $importedFileName
     * @param string|null $description
     *
     * @return ProcessingTemplate
     */
    public function createTemplate(string $name, string $jsonContent, string $importedFileName, ?string $description = null): ProcessingTemplate
    {
        $template = new ProcessingTemplate($name);
        $template->setData($jsonContent);
        $template->setImportedFileName($importedFileName);

        if ($description !== null) {
            $template->setDescription($description);
        }

        return $template;
    }

    /**
     * @param string       $name
     * @param UploadedFile $file
     * @param string|null  $description
     *
     * @return ProcessingTemplate
     */
    public function createTemplateWithFile(string $name, UploadedFile $file, ?string $description = null): ProcessingTemplate
    {
        $template = new ProcessingTemplate($name);
        $template->addFile($file);

        if ($description !== null) {
            $template->setDescription($description);
        }

        return $template;
    }
}
