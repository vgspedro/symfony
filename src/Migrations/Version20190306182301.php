<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306182301 extends AbstractMigration
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
        $this->addSql('ALTER TABLE company CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE link_facebook link_facebook VARCHAR(255) DEFAULT NULL, CHANGE link_twitter link_twitter VARCHAR(255) DEFAULT NULL, CHANGE link_instagram link_instagram VARCHAR(255) DEFAULT NULL, CHANGE link_linken link_linken VARCHAR(255) DEFAULT NULL, CHANGE link_pinterest link_pinterest VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company_translation CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE company CHANGE telephone telephone VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE logo logo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE link_facebook link_facebook VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE link_twitter link_twitter VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE link_instagram link_instagram VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE link_linken link_linken VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE link_pinterest link_pinterest VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE company_translation CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
