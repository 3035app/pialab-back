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
use PiaApi\Entity\Pia\Pia;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Version20180605082149 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function up(Schema $schema)
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            $rootFolder = $structure->getRootFolder();
            /** @var Pia $pia */
            foreach ($structure->getPias() as $pia) {
                $pia->setFolder($rootFolder);
            }
        }

        $doctrine->getManager()->flush();
    }

    public function down(Schema $schema)
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $pias = $doctrine->getRepository(Pia::class)->findAll();

        /** @var Pia $pia */
        foreach ($pias as $pia) {
            $pia->setFolder(null);
        }

        $doctrine->getManager()->flush();
    }

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
