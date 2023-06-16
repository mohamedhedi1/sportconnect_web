<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309145430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone INT NOT NULL, genre VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, likes VARCHAR(255) NOT NULL, dislikes VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom_equipement VARCHAR(255) NOT NULL, image_equipement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercice (id INT AUTO_INCREMENT NOT NULL, equipement_id INT DEFAULT NULL, nom_exercice VARCHAR(255) NOT NULL, image_exercice VARCHAR(255) NOT NULL, duration INT NOT NULL, repetation INT NOT NULL, instruction VARCHAR(255) NOT NULL, INDEX IDX_E418C74D806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantite INT DEFAULT NULL, calories INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interisse (id INT AUTO_INCREMENT NOT NULL, id_client VARCHAR(255) NOT NULL, id_serie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repas_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, heure TIME NOT NULL, image VARCHAR(255) NOT NULL, calories INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repas_entity_ingredient_entity (repas_entity_id INT NOT NULL, ingredient_entity_id INT NOT NULL, INDEX IDX_9D87921E308755B9 (repas_entity_id), INDEX IDX_9D87921E8B57D52A (ingredient_entity_id), PRIMARY KEY(repas_entity_id, ingredient_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, titre_serie VARCHAR(255) NOT NULL, image_serie VARCHAR(255) NOT NULL, valeur INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_exercice (serie_id INT NOT NULL, exercice_id INT NOT NULL, INDEX IDX_AC9E6875D94388BD (serie_id), INDEX IDX_AC9E687589D40298 (exercice_id), PRIMARY KEY(serie_id, exercice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74D806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE repas_entity_ingredient_entity ADD CONSTRAINT FK_9D87921E308755B9 FOREIGN KEY (repas_entity_id) REFERENCES repas_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repas_entity_ingredient_entity ADD CONSTRAINT FK_9D87921E8B57D52A FOREIGN KEY (ingredient_entity_id) REFERENCES ingredient_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_exercice ADD CONSTRAINT FK_AC9E6875D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_exercice ADD CONSTRAINT FK_AC9E687589D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74D806F0F5C');
        $this->addSql('ALTER TABLE repas_entity_ingredient_entity DROP FOREIGN KEY FK_9D87921E308755B9');
        $this->addSql('ALTER TABLE repas_entity_ingredient_entity DROP FOREIGN KEY FK_9D87921E8B57D52A');
        $this->addSql('ALTER TABLE serie_exercice DROP FOREIGN KEY FK_AC9E6875D94388BD');
        $this->addSql('ALTER TABLE serie_exercice DROP FOREIGN KEY FK_AC9E687589D40298');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE exercice');
        $this->addSql('DROP TABLE ingredient_entity');
        $this->addSql('DROP TABLE interisse');
        $this->addSql('DROP TABLE repas_entity');
        $this->addSql('DROP TABLE repas_entity_ingredient_entity');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP TABLE serie_exercice');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
