<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106112648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE enigme_type (enigme_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_A4542EBCA19FCF7 (enigme_id), INDEX IDX_A4542EBC54C8C93 (type_id), PRIMARY KEY(enigme_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enigme_type ADD CONSTRAINT FK_A4542EBCA19FCF7 FOREIGN KEY (enigme_id) REFERENCES enigme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enigme_type ADD CONSTRAINT FK_A4542EBC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enigme ADD consigne LONGTEXT NOT NULL, CHANGE type code_secret VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enigme_type DROP FOREIGN KEY FK_A4542EBCA19FCF7');
        $this->addSql('ALTER TABLE enigme_type DROP FOREIGN KEY FK_A4542EBC54C8C93');
        $this->addSql('DROP TABLE enigme_type');
        $this->addSql('ALTER TABLE enigme DROP consigne, CHANGE code_secret type VARCHAR(50) NOT NULL');
    }
}
