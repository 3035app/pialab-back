<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use PiaApi\Entity\Pia\Processing;

class Version20180911134822 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        // Change status Processing::STATUS_ARCHIVED old value (1) to new value (3)
        $this->addSql('UPDATE pia_processing SET status = 3 WHERE status = 1');
    }

    public function down(Schema $schema)
    {
        // Change status Processing::STATUS_ARCHIVED new value (3) to old value (1)
        $this->addSql('UPDATE pia_processing SET status = 1 WHERE status = 3');
    }
}
