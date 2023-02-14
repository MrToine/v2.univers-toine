<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214101538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historic_moderation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip_adress VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_98AF40EAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historic_moderation ADD CONSTRAINT FK_98AF40EAA76ED395 FOREIGN KEY (user_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE forum_topic CHANGE type type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historic_moderation DROP FOREIGN KEY FK_98AF40EAA76ED395');
        $this->addSql('DROP TABLE historic_moderation');
        $this->addSql('ALTER TABLE forum_topic CHANGE type type VARCHAR(50) DEFAULT NULL');
    }
}
