<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306181851 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(50) NOT NULL, p_code VARCHAR(10) NOT NULL, city VARCHAR(10) NOT NULL, country VARCHAR(20) NOT NULL, email VARCHAR(30) NOT NULL, email_pass VARCHAR(128) NOT NULL, telephone VARCHAR(20) NOT NULL, fiscal_number VARCHAR(50) NOT NULL, coords_google_maps VARCHAR(50) NOT NULL, google_maps_api_key VARCHAR(50) NOT NULL, logo VARCHAR(255) NOT NULL, link_facebook VARCHAR(255) NOT NULL, link_twitter VARCHAR(255) NOT NULL, link_instagram VARCHAR(255) NOT NULL, link_linken VARCHAR(255) NOT NULL, link_pinterest VARCHAR(255) NOT NULL, link_facebook_active TINYINT(1) DEFAULT \'0\' NOT NULL, link_twitter_active TINYINT(1) DEFAULT \'0\' NOT NULL, link_instagram_active TINYINT(1) DEFAULT \'0\' NOT NULL, link_linken_active TINYINT(1) DEFAULT \'0\' NOT NULL, link_pinterest_active TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_translation (id INT AUTO_INCREMENT NOT NULL, locale_id INT DEFAULT NULL, description LONGTEXT NOT NULL, INDEX IDX_ADB8FDF7E559DFD1 (locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_translation ADD CONSTRAINT FK_ADB8FDF7E559DFD1 FOREIGN KEY (locale_id) REFERENCES locales (id)');
        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_translation');
        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
