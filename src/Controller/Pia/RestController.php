<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Inflector as Inflector;
use PiaApi\Entity\Pia\Pia;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

abstract class RestController extends FOSRestController
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    private static $entityClasses = null;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Persists resource.
     *
     * @param mixed $entity
     */
    protected function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Updates resource.
     *
     * @param mixed $entity
     */
    protected function update($entity)
    {
        $this->getEntityManager()->merge($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Removes resource.
     *
     * @param mixed $entity
     */
    protected function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Gest resource entity manager.
     *
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Gets current resource entity repository.
     *
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->getDoctrine()->getRepository($this->getEntityClass());
    }

    /**
     * Gives a resource for specific ID for the given class.
     *
     * @param int         $id
     * @param string|null $entityclass
     */
    protected function getResource(int $id, ?string $entityclass = null)
    {
        $repo = $this->getRepository();
        if ($entityclass !== null) {
            $repo = $this->getDoctrine()->getRepository($entityclass);
        }

        return $repo->find($id);
    }

    /**
     * Merge a resource with its representation served in request.
     *
     * @param mixed   $entity
     * @param array   $attributesToMerge
     * @param Request $request
     */
    protected function mergeFromRequest($entity, array $attributesToMerge, Request $request): void
    {
        foreach ($attributesToMerge as $attributeToMerge => $attributeType) {
            $attributeData = $request->get($attributeToMerge);
            if ($this->isTypeADoctrineEntity($attributeType) && isset($attributeData['id'])) {
                $attributeData = $this->getResource($attributeData['id'], $attributeType);
            }

            $this->propertyAccessor->setValue($entity, $attributeToMerge, $attributeData);
        }
    }

    /**
     * Extracts data from request.
     *
     * @param Request $request
     * @param string  $key
     *
     * @return array
     */
    protected function extractData(Request $request, $key = null): array
    {
        $data = $request->request->all();
        //if ($key !== null) {
        //    $data = $data[$key];
        //}

        return array_filter($data, function ($item) {
            return $item != 'undefined';
        });
    }

    protected function extractCriteria(Request $request, $default = [])
    {
        $query = $request->query->all();
        if (empty($query)) {
            return $default;
        }
        $criteria = array_merge(...array_map(function ($key, $value) {
            return [Inflector::camelize($key) => $value];
        }, array_keys($query), $query));

        return array_merge($criteria, $default);
    }

    protected function extractPiaId(Request $request, $key = null)
    {
        $data = $request->request->all();
        if ($key !== null) {
            $data = $data[$key];
        }

        return $data['pia_id'] ?? $request->get('pia_id');
    }

    protected function newFromArray($data, $piaId = null)
    {
        $entity = $this->get('jms_serializer')->fromArray($data, $this->getEntityClass());
        if ($piaId !== null) {
            $entity->setPia($this->getEntityManager()->getReference(Pia::class, $piaId));
        }

        return $entity;
    }

    protected function newFromRequest(Request $request, $piaId = null)
    {
        $entity = $this->get('jms_serializer')->deserialize($request->getContent(), $this->getEntityClass(), 'json');
        if ($piaId !== null) {
            $entity->setPia($this->getEntityManager()->getReference(Pia::class, $piaId));
        }

        return $entity;
    }

    /**
     * Check that current User can access resource.
     *
     * @throws AccessDeniedHttpException
     */
    public function canAccessRouteOr403(): void
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException();
        }
    }

    public function canAccessResourceOr403($resource): void
    {
        // Each controllers should define this method to perform a fine access control
    }

    /**
     * Check if $type is an object managed by Doctrine.
     *
     * @param string $type
     *
     * @return bool
     */
    private function isTypeADoctrineEntity(string $type): bool
    {
        if (self::$entityClasses === null) {
            // Kind of cached list
            self::$entityClasses = [];
            foreach ($this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata() as $meta) {
                self::$entityClasses[] = $meta->getName();
            }
        }

        return in_array($type, self::$entityClasses);
    }

    abstract protected function getEntityClass();
}
