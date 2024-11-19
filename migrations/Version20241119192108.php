<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119192108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, name_action VARCHAR(255) NOT NULL, index_action VARCHAR(255) NOT NULL, INDEX IDX_47CC8C92BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asso_actions (association_id INT NOT NULL, action_id INT NOT NULL, INDEX IDX_25646DEFEFB9C8A5 (association_id), INDEX IDX_25646DEF9D32F035 (action_id), PRIMARY KEY(association_id, action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dowload_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, folder_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre (id INT AUTO_INCREMENT NOT NULL, association_id INT NOT NULL, statut_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, situation_maritale VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, INDEX IDX_F6B4FB29EFB9C8A5 (association_id), INDEX IDX_F6B4FB29F6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre_association (id INT AUTO_INCREMENT NOT NULL, membre_id INT NOT NULL, association_id INT NOT NULL, INDEX IDX_EEB303206A99F74A (membre_id), INDEX IDX_EEB30320EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE asso_actions ADD CONSTRAINT FK_25646DEFEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE asso_actions ADD CONSTRAINT FK_25646DEF9D32F035 FOREIGN KEY (action_id) REFERENCES action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB29EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB29F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE membre_association ADD CONSTRAINT FK_EEB303206A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE membre_association ADD CONSTRAINT FK_EEB30320EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92BCF5E72D');
        $this->addSql('ALTER TABLE asso_actions DROP FOREIGN KEY FK_25646DEFEFB9C8A5');
        $this->addSql('ALTER TABLE asso_actions DROP FOREIGN KEY FK_25646DEF9D32F035');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29EFB9C8A5');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29F6203804');
        $this->addSql('ALTER TABLE membre_association DROP FOREIGN KEY FK_EEB303206A99F74A');
        $this->addSql('ALTER TABLE membre_association DROP FOREIGN KEY FK_EEB30320EFB9C8A5');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE asso_actions');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE dowload_token');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE membre_association');
        $this->addSql('DROP TABLE statut');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
