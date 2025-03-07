<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307132629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, date DATE NOT NULL, place VARCHAR(255) NOT NULL, max_number_participants INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE participants (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE participants_events (participants_id INTEGER NOT NULL, events_id INTEGER NOT NULL, PRIMARY KEY(participants_id, events_id), CONSTRAINT FK_9E6008D8838709D5 FOREIGN KEY (participants_id) REFERENCES participants (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E6008D89D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9E6008D8838709D5 ON participants_events (participants_id)');
        $this->addSql('CREATE INDEX IDX_9E6008D89D6A1065 ON participants_events (events_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE participants_events');
    }
}
