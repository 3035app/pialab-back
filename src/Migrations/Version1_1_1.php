<?php

declare(strict_types=1);

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
<<<<<<< HEAD
 * This file is licenced under the GNU LGPL v3.
=======
 * This file is licensed under the GNU LGPL v3.
>>>>>>> 3409f3559d9ea36a4d4201666110cafdcf3bd661
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use PiaApi\Migrations\Lib\MigrationTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use PiaApi\Entity\Pia\UserProfile;
use PiaApi\Entity\Oauth\User;

class Version1_1_1 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180621082800',
        ],
        'data' => [
        ],
    ];

    protected function Version20180621082800_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("UPDATE pia set concerned_people_searched_opinion=false where concerned_people_sear ched_opinion IS NULL");
    }

    protected function Version20180621082800_down(Schema $schema): void
    {
        // Do nothing
    }
}
