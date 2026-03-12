<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312101237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parametre (id INT AUTO_INCREMENT NOT NULL, jeu_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, valeur VARCHAR(255) NOT NULL, INDEX IDX_ACC790418C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parametre ADD CONSTRAINT FK_ACC790418C9E392E FOREIGN KEY (jeu_id) REFERENCES tbl_jeu (id)');
        $this->addSql('ALTER TABLE tbl_parametre DROP FOREIGN KEY FK_E94FFFD28C9E392E');
        $this->addSql('DROP TABLE tbl_parametre');
        $this->addSql('ALTER TABLE tbl_avatar DROP FOREIGN KEY FK_9BD11B906D861B89');
        $this->addSql('DROP INDEX IDX_9BD11B906D861B89 ON tbl_avatar');
        $this->addSql('ALTER TABLE tbl_avatar DROP equipe_id');
        $this->addSql('ALTER TABLE tbl_enigme ADD code_reponse VARCHAR(2) DEFAULT NULL, ADD choices JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tbl_equipe ADD avatar_id INT NOT NULL, ADD started_at DATETIME DEFAULT NULL, ADD finished_at DATETIME DEFAULT NULL, DROP note');
        $this->addSql('ALTER TABLE tbl_equipe ADD CONSTRAINT FK_A9EFD3AA86383B10 FOREIGN KEY (avatar_id) REFERENCES tbl_avatar (id)');
        $this->addSql('CREATE INDEX IDX_A9EFD3AA86383B10 ON tbl_equipe (avatar_id)');
        $this->addSql('ALTER TABLE tbl_jeu ADD code_final VARCHAR(50) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_parametre (id INT AUTO_INCREMENT NOT NULL, jeu_id INT DEFAULT NULL, INDEX IDX_E94FFFD28C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tbl_parametre ADD CONSTRAINT FK_E94FFFD28C9E392E FOREIGN KEY (jeu_id) REFERENCES tbl_jeu (id)');
        $this->addSql('ALTER TABLE parametre DROP FOREIGN KEY FK_ACC790418C9E392E');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('ALTER TABLE tbl_avatar ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_avatar ADD CONSTRAINT FK_9BD11B906D861B89 FOREIGN KEY (equipe_id) REFERENCES tbl_equipe (id)');
        $this->addSql('CREATE INDEX IDX_9BD11B906D861B89 ON tbl_avatar (equipe_id)');
        $this->addSql('ALTER TABLE tbl_enigme DROP code_reponse, DROP choices');
        $this->addSql('ALTER TABLE tbl_equipe DROP FOREIGN KEY FK_A9EFD3AA86383B10');
        $this->addSql('DROP INDEX IDX_A9EFD3AA86383B10 ON tbl_equipe');
        $this->addSql('ALTER TABLE tbl_equipe ADD note LONGTEXT NOT NULL, DROP avatar_id, DROP started_at, DROP finished_at');
        $this->addSql('ALTER TABLE tbl_jeu DROP code_final');
    }
}
