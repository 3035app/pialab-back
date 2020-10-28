<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

abstract class EntitySearchChoiceType extends AbstractType
{
    protected $repository;

    protected $choices = null;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->choices === null) {
            $this->choices = $this->repository->findAll();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $me = $this;
        $resolver->setDefaults([
            'required'       => false,
            'class'          => $this->repository->getClassName(),
            'choice_label'   => 'name',
            'multiple'       => false,
            'expanded'       => false,
            'choices'        => $this->getChoices(),
            'hidden_choices' => [],
            'choice_loader'  => function (Options $options) use ($me) {
                return new CallbackChoiceLoader(function () use ($me, $options) {
                    return array_filter($me->getChoices(), function ($choice) use ($options) {
                        return !in_array($choice, $options['hidden_choices']);
                    });
                });
            },
        ]);
    }

    private function getChoices(): array
    {
        return $this->choices ?? [];
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
