<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160920060941 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cat ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cat DROP creator');
        $this->addSql('ALTER TABLE cat ADD CONSTRAINT FK_9E5E43A861220EA6 FOREIGN KEY (creator_id) REFERENCES user_credentials (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9E5E43A861220EA6 ON cat (creator_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cat DROP CONSTRAINT FK_9E5E43A861220EA6');
        $this->addSql('DROP INDEX IDX_9E5E43A861220EA6');
        $this->addSql('ALTER TABLE cat ADD creator VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cat DROP creator_id');
    }
}
