<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Structure;

use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Form\Portfolio\Type\PortfolioChoiceType;
use PiaApi\Form\Structure\Type\StructureTypeChoiceType;
use PiaApi\Entity\Pia\Portfolio;

class CreateStructureForm extends BaseForm
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label'    => 'pia.structures.forms.create.name',
            ])
            ->add('type', StructureTypeChoiceType::class, [
                'required' => false,
                'label'    => 'pia.structures.forms.create.type',
            ]);

        $builder
            ->add('portfolio', PortfolioChoiceType::class, [
                'required' => false,
                'label'    => 'pia.structures.forms.create.portfolio',
            ]);

        $builder
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.structures.forms.create.submit',
            ]);

        if (!count($builder->get('portfolio')->getOption('choices'))) {
            $builder->remove('portfolio');
        }
    }
}
