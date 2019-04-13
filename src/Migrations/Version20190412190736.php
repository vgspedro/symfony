<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190412190736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE company CHANGE email_smtp email_smtp VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE company CHANGE email_smtp email_smtp VARCHAR(24) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
