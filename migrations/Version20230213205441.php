<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213205441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet ADD chef_projet_id INT NOT NULL, ADD resume VARCHAR(255) DEFAULT NULL, ADD financement VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9D3B0D67C FOREIGN KEY (chef_projet_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_50159CA9D3B0D67C ON projet (chef_projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9D3B0D67C');
        $this->addSql('DROP INDEX IDX_50159CA9D3B0D67C ON projet');
        $this->addSql('ALTER TABLE projet DROP chef_projet_id, DROP resume, DROP financement');
    }
}
