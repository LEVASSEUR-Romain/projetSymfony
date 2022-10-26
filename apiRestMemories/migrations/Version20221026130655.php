<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221026130655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE list_card (id INT AUTO_INCREMENT NOT NULL, list_id_id INT DEFAULT NULL, card_id_id INT DEFAULT NULL, INDEX IDX_53DA473AA6D70A54 (list_id_id), INDEX IDX_53DA473A47706F91 (card_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE list_card ADD CONSTRAINT FK_53DA473AA6D70A54 FOREIGN KEY (list_id_id) REFERENCES list_memory (id)');
        $this->addSql('ALTER TABLE list_card ADD CONSTRAINT FK_53DA473A47706F91 FOREIGN KEY (card_id_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE card_memory DROP FOREIGN KEY FK_9852674147706F91');
        $this->addSql('ALTER TABLE card_memory DROP FOREIGN KEY FK_985267419853803B');
        $this->addSql('DROP TABLE card_memory');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_memory (id INT AUTO_INCREMENT NOT NULL, memory_id_id INT DEFAULT NULL, card_id_id INT DEFAULT NULL, INDEX IDX_985267419853803B (memory_id_id), INDEX IDX_9852674147706F91 (card_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE card_memory ADD CONSTRAINT FK_9852674147706F91 FOREIGN KEY (card_id_id) REFERENCES cards (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE card_memory ADD CONSTRAINT FK_985267419853803B FOREIGN KEY (memory_id_id) REFERENCES card_memory (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE list_card DROP FOREIGN KEY FK_53DA473AA6D70A54');
        $this->addSql('ALTER TABLE list_card DROP FOREIGN KEY FK_53DA473A47706F91');
        $this->addSql('DROP TABLE list_card');
    }
}
