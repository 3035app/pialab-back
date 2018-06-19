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

class Version1_1_0 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180612141711',
        ],
        'data' => [
            '20180619143737',
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################

    protected function Version20180612141711_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia ADD type VARCHAR(255) NULL');
        $this->addSql("UPDATE pia set type='advanced'");
        $this->addSql('ALTER TABLE pia ALTER COLUMN type SET NOT NULL');
    }

    protected function Version20180612141711_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP type');
    }

    protected function Version20180619143737_up(Schema $schema): void
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $users = $doctrine->getRepository(User::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->getProfile() === null) {
                $profile = new UserProfile();
                $profile->setUser($user);
                $user->setProfile($profile);

                $doctrine->getManager()->persist($profile);
                $doctrine->getManager()->flush();
            }
        }

        $emptyProfile = $doctrine->getRepository(UserProfile::class)->findBy(['user' => null]);

        /** @var UserProfile $profile */
        foreach ($emptyProfile as $profile) {
            $doctrine->getManager()->remove($profile);
        }
        $doctrine->getManager()->flush();
    }

    protected function Version20180619143737_down(Schema $schema): void
    {
        // Do nothing
    }
}
