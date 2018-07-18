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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use PiaApi\Form\Portfolio\Type\PortfolioChoiceType;
use PiaApi\Form\Structure\Type\StructureChoiceType;
use PiaApi\Entity\Pia\Portfolio;

class StructurePortfolioAssocForm extends BaseForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $portfolio = $options['portfolio'];

        if (!$portfolio) {
            $builder
                    ->add('portfolio', PortfolioChoiceType::class, [
                        'required' => false,
                        'label'    => 'pia.structures.forms.assoc.portfolio',
                    ]);
        } else {
            $builder
                    ->add('portfolio', HiddenType::class, [
                        'required'   => true,
                        'data'       => $portfolio->getId(),
                        'data_class' => null,
                    ]);
        }
        $builder
            ->add('structures', StructureChoiceType::class, [
                'required'       => false,
                'multiple'       => true,
                'label'          => 'pia.structures.forms.assoc.structures',
                'hidden_choices' => $portfolio ? $portfolio->getStructures() : [],
            ]);

        $builder
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.structures.forms.assoc.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'portfolio' => false,
        ]);
    }
}
