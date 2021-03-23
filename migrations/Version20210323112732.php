<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323112732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A257E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DADD4A257E3C61F9 ON answer (owner_id)');
        $this->addSql('ALTER TABLE deck ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deck ADD version INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deck ADD CONSTRAINT FK_4FAC3637727ACA70 FOREIGN KEY (parent_id) REFERENCES deck (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FAC3637727ACA70 ON deck (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE deck DROP CONSTRAINT FK_4FAC3637727ACA70');
        $this->addSql('DROP INDEX IDX_4FAC3637727ACA70');
        $this->addSql('ALTER TABLE deck DROP parent_id');
        $this->addSql('ALTER TABLE deck DROP version');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A257E3C61F9');
        $this->addSql('DROP INDEX IDX_DADD4A257E3C61F9');
        $this->addSql('ALTER TABLE answer DROP owner_id');
    }
}
