<?php

namespace PiaApi\Form\Application;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use OAuth2\OAuth2;

class EditApplicationForm extends CreateApplicationForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('submit')
            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;'
                ],
                'label' => 'Annuler'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;'
                ],
                'label' => 'Enregistrer l\'application'
            ])
        ;
    }
}
