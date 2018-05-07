<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

$header = <<<EOF
Copyright (C) 2015-%end_copy_right_year% Libre Informatique

This file is licenced under the GNU LGPL v3.
For the full copyright and license information, please view the LICENSE.md
file that was distributed with this source code.
EOF;

$header = preg_replace('/%end_copy_right_year%/', date('Y'), $header);

// PHP-CS-Fixer 1.x
if (class_exists('Symfony\CS\Fixer\Contrib\HeaderCommentFixer')) {
    \Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);
}

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony'               => true,
        'binary_operator_spaces' => ['align_double_arrow' => true],
        'concat_space'           => ['spacing'=>'one'],
        'yoda_style'             => null,
        'increment_style'        => ['style' => 'post'],
    ))
    ->setFinder($finder);

// PHP-CS-Fixer 2.x
if (method_exists($config, 'setRules')) {
    $config->setRules(array_merge($config->getRules(), array(
        'header_comment' => array('header' => $header),
    )));
}

return $config;
