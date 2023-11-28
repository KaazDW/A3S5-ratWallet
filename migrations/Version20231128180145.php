<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128180145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD goal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4667D1AFE ON account (goal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4667D1AFE');
        $this->addSql('DROP INDEX UNIQ_7D3656A4667D1AFE ON account');
        $this->addSql('ALTER TABLE account DROP goal_id');
    }
}
