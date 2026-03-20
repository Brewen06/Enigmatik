<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320132652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametre ADD choix LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE tbl_avatar DROP FOREIGN KEY FK_9BD11B906D861B89');
        $this->addSql('DROP INDEX IDX_9BD11B906D861B89 ON tbl_avatar');
        $this->addSql('ALTER TABLE tbl_avatar DROP equipe_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametre DROP choix');
        $this->addSql('ALTER TABLE tbl_avatar ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_avatar ADD CONSTRAINT FK_9BD11B906D861B89 FOREIGN KEY (equipe_id) REFERENCES tbl_equipe (id)');
        $this->addSql('CREATE INDEX IDX_9BD11B906D861B89 ON tbl_avatar (equipe_id)');
    }
}
