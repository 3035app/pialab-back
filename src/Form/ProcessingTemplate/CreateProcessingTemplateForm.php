<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\ProcessingTemplate;

use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CreateProcessingTemplateForm extends BaseForm
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label'    => 'pia.templates.forms.create.name',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label'    => 'pia.templates.forms.create.description',
            ])
            ->add('data', FileType::class, [
                'label'    => 'pia.templates.forms.create.data',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.templates.forms.create.submit',
            ])
        ;
    }
}
