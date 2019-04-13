<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190412073101 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE company_translation');
        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE company ADD meta_keywords LONGTEXT DEFAULT NULL, ADD meta_description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company_translation (id INT AUTO_INCREMENT NOT NULL, locale_id INT DEFAULT NULL, company_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_ADB8FDF7979B1AD6 (company_id), INDEX IDX_ADB8FDF7E559DFD1 (locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE company_translation ADD CONSTRAINT FK_ADB8FDF7979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_translation ADD CONSTRAINT FK_ADB8FDF7E559DFD1 FOREIGN KEY (locale_id) REFERENCES locales (id)');
        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE company DROP meta_keywords, DROP meta_description');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
