<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128192051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insertion de champ dans la table Category';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO category (`id`, `label`) VALUES ('1', 'EAU');");
        $this->addSql("INSERT INTO category (`id`, `label`) VALUES ('2', 'EDF');");
        $this->addSql("INSERT INTO category (`id`, `label`) VALUES ('3', 'Loisir');");
        $this->addSql("INSERT INTO category (`id`, `label`) VALUES ('4', 'Vacance');");
        $this->addSql("INSERT INTO category (`label`) VALUES ('Autre');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM category WHERE `label` = 'EAU';");
        $this->addSql("DELETE FROM category WHERE `label` = 'EDF';");
        $this->addSql("DELETE FROM category WHERE `label` = 'Loisir';");
        $this->addSql("DELETE FROM category WHERE `label` = 'Vacance';");
        $this->addSql("DELETE FROM category WHERE `label` = 'Autre';");
    }
}
