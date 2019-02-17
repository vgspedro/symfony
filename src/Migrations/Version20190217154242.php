<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190217154242 extends AbstractMigration
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
        $this->addSql('ALTER TABLE client CHANGE cvv cvv LONGTEXT DEFAULT NULL, CHANGE card_name card_name LONGTEXT DEFAULT NULL, CHANGE card_nr card_nr LONGTEXT DEFAULT NULL, CHANGE card_date card_date LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE client CHANGE cvv cvv VARCHAR(4) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE card_name card_name VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE card_nr card_nr VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE card_date card_date VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
