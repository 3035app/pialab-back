<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Structure\Type;

use PiaApi\Form\Type\EntitySearchChoiceType;
use PiaApi\Repository\StructureRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class StructureChoiceType extends EntitySearchChoiceType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    public function __construct(
        StructureRepository $repository,
        AuthorizationCheckerInterface $authChecker,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($repository);
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authChecker->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES')) {
            // Limit structures to owned ones in order to allow shared DPO to move user from one structure to another of its portfolios.
            $user = $this->tokenStorage->getToken()->getUser();
            $this->choices = $this->repository->getStructuresForPortfolios($user->getPortfolios());
        }

        parent::buildForm($builder, $options);
    }
}
