<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004144727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE membre_association (id INT AUTO_INCREMENT NOT NULL, membre_id INT NOT NULL, association_id INT NOT NULL, INDEX IDX_EEB303206A99F74A (membre_id), INDEX IDX_EEB30320EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE membre_association ADD CONSTRAINT FK_EEB303206A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE membre_association ADD CONSTRAINT FK_EEB30320EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre_association DROP FOREIGN KEY FK_EEB303206A99F74A');
        $this->addSql('ALTER TABLE membre_association DROP FOREIGN KEY FK_EEB30320EFB9C8A5');
        $this->addSql('DROP TABLE membre_association');
    }
}
