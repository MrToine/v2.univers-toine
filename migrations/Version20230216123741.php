<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216123741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item ADD type VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX `primary` ON member_item');
        $this->addSql('ALTER TABLE member_item ADD PRIMARY KEY (member_id, item_id)');
        $this->addSql('ALTER TABLE member_item RENAME INDEX idx_f61d8fb77597d3fe TO IDX_E39309427597D3FE');
        $this->addSql('ALTER TABLE member_item RENAME INDEX idx_f61d8fb7126f525e TO IDX_E3930942126F525E');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON member_item');
        $this->addSql('ALTER TABLE member_item ADD PRIMARY KEY (item_id, member_id)');
        $this->addSql('ALTER TABLE member_item RENAME INDEX idx_e3930942126f525e TO IDX_F61D8FB7126F525E');
        $this->addSql('ALTER TABLE member_item RENAME INDEX idx_e39309427597d3fe TO IDX_F61D8FB77597D3FE');
        $this->addSql('ALTER TABLE item DROP type');
    }
}
