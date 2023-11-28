<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128181409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE debt (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, category_id INT DEFAULT NULL, debt_amount DOUBLE PRECISION NOT NULL, creditor VARCHAR(255) NOT NULL, deadline DATETIME DEFAULT NULL, INDEX IDX_DBBF0A839B6B5FBA (account_id), INDEX IDX_DBBF0A8312469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE debt ADD CONSTRAINT FK_DBBF0A839B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE debt ADD CONSTRAINT FK_DBBF0A8312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE debt DROP FOREIGN KEY FK_DBBF0A839B6B5FBA');
        $this->addSql('ALTER TABLE debt DROP FOREIGN KEY FK_DBBF0A8312469DE2');
        $this->addSql('DROP TABLE debt');
    }
}
