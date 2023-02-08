<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208184639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD3995069CCBE9A FOREIGN KEY (author_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_1DD3995069CCBE9A ON news (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD3995069CCBE9A');
        $this->addSql('DROP INDEX IDX_1DD3995069CCBE9A ON news');
        $this->addSql('ALTER TABLE news DROP author_id_id, CHANGE thumbnail thumbnail VARCHAR(255) DEFAULT NULL, CHANGE top_list_enabled top_list_enabled LONGTEXT DEFAULT NULL');
    }
}
