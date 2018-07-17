<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityDataTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $entityClass;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function forEntity(string $entityClass): DataTransformerInterface
    {
        $transformer = clone $this;

        $transformer->setEntityClass($entityClass);

        return $transformer;
    }

    /**
     * Transforms an entity to a string (ID).
     *
     * @param mixed|null $entity
     *
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * Transforms a string (ID) to an entity.
     *
     * @param string $entityId
     *
     * @return mixed|null
     *
     * @throws TransformationFailedException if entity is not found
     */
    public function reverseTransform($entityId)
    {
        if (!$entityId) {
            return;
        }

        if (null === $this->entityClass) {
            throw new TransformationFailedException(
                'You must define the target entity class for EntityDataTransformer'
            );
        }

        $issue = $this->entityManager
            ->getRepository($this->entityClass)
            ->find($entityId);

        if (null === $issue) {
            throw new TransformationFailedException(sprintf(
                'Entity with ID "%s" has not been found!',
                $entityId
            ));
        }

        return $issue;
    }

    /**
     * @param string $entityClass null
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }
}
