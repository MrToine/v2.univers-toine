<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216114741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, img VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_member (item_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_F61D8FB7126F525E (item_id), INDEX IDX_F61D8FB77597D3FE (member_id), PRIMARY KEY(item_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_member ADD CONSTRAINT FK_F61D8FB7126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_member ADD CONSTRAINT FK_F61D8FB77597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `member` CHANGE theme theme VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_member DROP FOREIGN KEY FK_F61D8FB7126F525E');
        $this->addSql('ALTER TABLE item_member DROP FOREIGN KEY FK_F61D8FB77597D3FE');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_member');
        $this->addSql('ALTER TABLE `member` CHANGE theme theme VARCHAR(255) DEFAULT \'default\'');
    }
}
