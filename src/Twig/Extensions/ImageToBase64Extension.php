<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Twig\Extensions;

use Twig\Extension\AbstractExtension;

class ImageToBase64Extension extends AbstractExtension
{
    /**
     * @var string
     */
    private $KernelProjectDir;

    public function __construct(string $KernelProjectDir)
    {
        $this->KernelProjectDir = $KernelProjectDir;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('imageToBase64', [$this, 'convertImageToBase64']),
        ];
    }

    public function convertImageToBase64(string $imagePath): string
    {
        $filePath = $this->KernelProjectDir . '/public/' . $imagePath;

        $base64String = sprintf(
            'data:%s;base64,%s',
            mime_content_type($filePath),
            base64_encode(file_get_contents($filePath))
        );

        return $base64String;
    }
}
