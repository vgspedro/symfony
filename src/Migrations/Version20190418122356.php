<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418122356 extends AbstractMigration
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
        $this->addSql('ALTER TABLE category CHANGE description_pt description_pt VARCHAR(1000) NOT NULL, CHANGE description_en description_en VARCHAR(1000) NOT NULL, CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(1000) DEFAULT NULL, CHANGE warranty_payment_en warranty_payment_en VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE category CHANGE description_pt description_pt VARCHAR(350) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description_en description_en VARCHAR(350) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(350) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE warranty_payment_en warranty_payment_en VARCHAR(350) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
