<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectRepository;

abstract class EntitySearchChoiceType extends AbstractType
{
    private $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => $this->repository->getClassName(),
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
            'choices'  => $this->getChoices(),
        ]);
    }

    private function getChoices(): array
    {
        return $this->repository->findAll();
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
