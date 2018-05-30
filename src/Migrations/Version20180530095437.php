<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\Folder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Version20180530095437 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function up(Schema $schema)
    {
        // Create and associate mandatory rootFolders for each structures

        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            if ($structure->getRootFolder() === null) {
                $rootFolder = new Folder('root', $structure);
                $doctrine->getManager()->persist($rootFolder);
                $doctrine->getManager()->flush($rootFolder);
            }
        }
    }

    public function down(Schema $schema)
    {
        // Dissociate mandatory rootFolders for each structures

        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            if (($rootFolder = $structure->getRootFolder()) !== null) {
                $rootFolder->setStructure(null);
                $doctrine->getManager()->flush($rootFolder);
            }
        }
    }

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
