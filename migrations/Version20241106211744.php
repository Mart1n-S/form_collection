<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106211744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association ADD action_id INT NOT NULL');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC9D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CC9D32F035 ON association (action_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC9D32F035');
        $this->addSql('DROP INDEX UNIQ_FD8521CC9D32F035 ON association');
        $this->addSql('ALTER TABLE association DROP action_id');
    }
}
