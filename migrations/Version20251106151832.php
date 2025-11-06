<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106151832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_avatar (id INT AUTO_INCREMENT NOT NULL, equipe_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, INDEX IDX_9BD11B906D861B89 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_enigme (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, vignette_id INT DEFAULT NULL, ordre INT NOT NULL, titre VARCHAR(50) NOT NULL, consigne LONGTEXT NOT NULL, code_secret VARCHAR(50) NOT NULL, INDEX IDX_A4627AC8C54C8C93 (type_id), UNIQUE INDEX UNIQ_A4627AC87D16298B (vignette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_equipe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, position INT NOT NULL, note LONGTEXT NOT NULL, enigme_actuelle INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_jeu (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(50) NOT NULL, message_de_bienvenue LONGTEXT DEFAULT NULL, image_bienvenue VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_parametres (id INT AUTO_INCREMENT NOT NULL, jeu_id INT DEFAULT NULL, INDEX IDX_733A330C8C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_type (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_vignette (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, information LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_avatar ADD CONSTRAINT FK_9BD11B906D861B89 FOREIGN KEY (equipe_id) REFERENCES tbl_equipe (id)');
        $this->addSql('ALTER TABLE tbl_enigme ADD CONSTRAINT FK_A4627AC8C54C8C93 FOREIGN KEY (type_id) REFERENCES tbl_type (id)');
        $this->addSql('ALTER TABLE tbl_enigme ADD CONSTRAINT FK_A4627AC87D16298B FOREIGN KEY (vignette_id) REFERENCES tbl_vignette (id)');
        $this->addSql('ALTER TABLE tbl_parametres ADD CONSTRAINT FK_733A330C8C9E392E FOREIGN KEY (jeu_id) REFERENCES tbl_jeu (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_avatar DROP FOREIGN KEY FK_9BD11B906D861B89');
        $this->addSql('ALTER TABLE tbl_enigme DROP FOREIGN KEY FK_A4627AC8C54C8C93');
        $this->addSql('ALTER TABLE tbl_enigme DROP FOREIGN KEY FK_A4627AC87D16298B');
        $this->addSql('ALTER TABLE tbl_parametres DROP FOREIGN KEY FK_733A330C8C9E392E');
        $this->addSql('DROP TABLE tbl_avatar');
        $this->addSql('DROP TABLE tbl_enigme');
        $this->addSql('DROP TABLE tbl_equipe');
        $this->addSql('DROP TABLE tbl_jeu');
        $this->addSql('DROP TABLE tbl_parametres');
        $this->addSql('DROP TABLE tbl_type');
        $this->addSql('DROP TABLE tbl_user');
        $this->addSql('DROP TABLE tbl_vignette');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
