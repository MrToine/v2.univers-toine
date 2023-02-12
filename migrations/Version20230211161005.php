<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211161005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `member` ADD citation VARCHAR(255) DEFAULT NULL, ADD signature LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE news CHANGE content content LONGTEXT DEFAULT NULL, CHANGE published published INT NOT NULL, CHANGE thumbnail thumbnail VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news CHANGE content content LONGTEXT NOT NULL, CHANGE published published INT DEFAULT 1, CHANGE thumbnail thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `member` DROP citation, DROP signature');
    }
}
