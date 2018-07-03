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
use PiaApi\Form\Structure\Transformer\StructureTypeTransformer;
use PiaApi\Entity\Pia\StructureType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CreateStructureForm extends BaseForm
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var StructureTypeTransformer
     */
    protected $structureTypeTransformer;

    public function __construct(RegistryInterface $doctrine, StructureTypeTransformer $structureTypeTransformer)
    {
        $this->doctrine = $doctrine;
        $this->structureTypeTransformer = $structureTypeTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label'    => 'pia.structures.forms.create.name',
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices'  => $this->getStructureTypes(),
                'label'    => 'pia.structures.forms.create.type',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.structures.forms.create.submit',
            ])
        ;

        $builder->get('type')->addModelTransformer($this->structureTypeTransformer);
    }

    private function getStructureTypes(): array
    {
        $structureTypes = [];

        foreach ($this->doctrine->getManager()->getRepository(StructureType::class)->findAll() as $structureType) {
            $structureTypes[$structureType->getId()] = $structureType->getName() ?? $structureType->getId();
        }

        return array_flip($structureTypes);
    }
}
