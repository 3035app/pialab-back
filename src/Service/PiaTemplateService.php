<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Service;

use PiaApi\Entity\Pia\PiaTemplate;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PiaTemplateService
{
    /**
     * @param string      $name
     * @param string      $jsonContent
     * @param string      $importedFileName
     * @param string|null $description
     *
     * @return PiaTemplate
     */
    public function newTemplate(string $name, string $jsonContent, string $importedFileName, ?string $description = null): PiaTemplate
    {
        $template = new PiaTemplate($name);
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
     * @return PiaTemplate
     */
    public function newTemplateWithFile(string $name, UploadedFile $file, ?string $description = null): PiaTemplate
    {
        $template = new PiaTemplate($name);
        $template->addFile($file);

        if ($description !== null) {
            $template->setDescription($description);
        }

        return $template;
    }
}
