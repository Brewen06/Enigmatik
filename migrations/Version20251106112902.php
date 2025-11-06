<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106112902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enigme ADD vignette_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE enigme ADD CONSTRAINT FK_29C413777D16298B FOREIGN KEY (vignette_id) REFERENCES vignette (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29C413777D16298B ON enigme (vignette_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enigme DROP FOREIGN KEY FK_29C413777D16298B');
        $this->addSql('DROP INDEX UNIQ_29C413777D16298B ON enigme');
        $this->addSql('ALTER TABLE enigme DROP vignette_id');
    }
}
