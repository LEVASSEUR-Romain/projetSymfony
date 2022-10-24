<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024120919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_memory ADD memory_id_id INT DEFAULT NULL, ADD card_id_id INT DEFAULT NULL, DROP memory_id, DROP card_id');
        $this->addSql('ALTER TABLE card_memory ADD CONSTRAINT FK_985267419853803B FOREIGN KEY (memory_id_id) REFERENCES card_memory (id)');
        $this->addSql('ALTER TABLE card_memory ADD CONSTRAINT FK_9852674147706F91 FOREIGN KEY (card_id_id) REFERENCES cards (id)');
        $this->addSql('CREATE INDEX IDX_985267419853803B ON card_memory (memory_id_id)');
        $this->addSql('CREATE INDEX IDX_9852674147706F91 ON card_memory (card_id_id)');
        $this->addSql('ALTER TABLE cards ADD user_id_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE cards ADD CONSTRAINT FK_4C258FD9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4C258FD9D86650F ON cards (user_id_id)');
        $this->addSql('ALTER TABLE list_memory ADD user_id_id INT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE list_memory ADD CONSTRAINT FK_73BDF61B9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_73BDF61B9D86650F ON list_memory (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_memory DROP FOREIGN KEY FK_985267419853803B');
        $this->addSql('ALTER TABLE card_memory DROP FOREIGN KEY FK_9852674147706F91');
        $this->addSql('DROP INDEX IDX_985267419853803B ON card_memory');
        $this->addSql('DROP INDEX IDX_9852674147706F91 ON card_memory');
        $this->addSql('ALTER TABLE card_memory ADD memory_id INT NOT NULL, ADD card_id INT NOT NULL, DROP memory_id_id, DROP card_id_id');
        $this->addSql('ALTER TABLE cards DROP FOREIGN KEY FK_4C258FD9D86650F');
        $this->addSql('DROP INDEX IDX_4C258FD9D86650F ON cards');
        $this->addSql('ALTER TABLE cards ADD user_id INT NOT NULL, DROP user_id_id');
        $this->addSql('ALTER TABLE list_memory DROP FOREIGN KEY FK_73BDF61B9D86650F');
        $this->addSql('DROP INDEX IDX_73BDF61B9D86650F ON list_memory');
        $this->addSql('ALTER TABLE list_memory ADD user_id INT NOT NULL, DROP user_id_id, DROP description');
    }
}
