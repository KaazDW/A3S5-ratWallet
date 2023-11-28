<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128192814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insertion de champ dans les tables accountType et Currency';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO account_type (`label`) VALUES ( 'Carte bancaire');");
        $this->addSql("INSERT INTO account_type ( `label`) VALUES ('Banque');");
        $this->addSql("INSERT INTO account_type ( `label`) VALUES ( 'Epargne');");
        $this->addSql("INSERT INTO currency (`label`) VALUES ( 'Euro');");
        $this->addSql("INSERT INTO currency (`label`) VALUES ('Dollar');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM account_type WHERE `label` = 'Carte bancaire';");
        $this->addSql("DELETE FROM account_type WHERE `label` = 'Banque';");
        $this->addSql("DELETE FROM account_type WHERE `label` = 'Epargne';");
        $this->addSql("DELETE FROM currency WHERE `label` = 'Euro';");
        $this->addSql("DELETE FROM currency WHERE `label` = 'Dollar';");
    }
}
