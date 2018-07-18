<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use Doctrine\ORM\EntityManagerInterface;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Repository\PortfolioRepository;

final class PortfolioService
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, PortfolioRepository $portfolioRepository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $portfolioRepository;
    }

    /**
     * @param string $name
     *
     * @return Portfolio
     */
    public function newPortfolio(string $name): Portfolio
    {
        return new Portfolio($name);
    }

    /**
     * @param string $name
     *
     * @return Portfolio
     */
    public function newFromFormData(array $data): Portfolio
    {
        $p = new Portfolio($data['name']);
        $p->setStructures($data['structures'] ?? []);

        return $p;
    }

    /**
     * @param string $id
     *
     * @return Portfolio
     */
    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function save(Portfolio $portfolio): void
    {
        $this->entityManager->flush();
    }

    public function remove(Portfolio $portfolio): void
    {
        foreach ($portfolio->getUsers() as $user) {
            $user->removePortfolio($portfolio);
        }

        foreach ($portfolio->getStructures() as $structure) {
            $structure->setPortfolio(null);
        }

        $this->entityManager->remove($portfolio);
        $this->entityManager->flush();
    }
}
