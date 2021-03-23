<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323160757 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_deck (user_id INT NOT NULL, deck_id INT NOT NULL, PRIMARY KEY(user_id, deck_id))');
        $this->addSql('CREATE INDEX IDX_352D7AFEA76ED395 ON user_deck (user_id)');
        $this->addSql('CREATE INDEX IDX_352D7AFE111948DC ON user_deck (deck_id)');
        $this->addSql('ALTER TABLE user_deck ADD CONSTRAINT FK_352D7AFEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_deck ADD CONSTRAINT FK_352D7AFE111948DC FOREIGN KEY (deck_id) REFERENCES deck (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE user_deck');
    }
}
